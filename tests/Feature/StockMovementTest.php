<?php

namespace Tests\Feature;

use App\Exceptions\StockException;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    private User $otherStaff;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
        $this->otherStaff = User::factory()->staff()->create();
        $this->product = Product::factory()->ownedBy($this->staff)->quantity(50)->create();
    }

    private function record(User $user, array $data, string $area = 'staff')
    {
        return $this->actingAs($user)->post("/{$area}/stock-movements", array_merge(['product_id' => $this->product->id], $data));
    }

    // ---------- Business rules through HTTP ----------

    public function test_stock_in_increases_quantity_and_records_history(): void
    {
        $this->record($this->staff, ['type' => 'IN', 'quantity' => 20, 'reason' => 'Delivery'])
            ->assertRedirect(route('staff.products.show', $this->product))
            ->assertSessionHas('success');

        $this->assertSame(70, $this->product->fresh()->quantity);

        $movement = StockMovement::sole();
        $this->assertSame('IN', $movement->type);
        $this->assertSame(20, $movement->quantity);
        $this->assertSame(50, $movement->previous_quantity);
        $this->assertSame(70, $movement->new_quantity);
        $this->assertSame($this->staff->id, $movement->user_id);
        $this->assertSame('Delivery', $movement->reason);
    }

    public function test_stock_out_decreases_quantity(): void
    {
        $this->record($this->staff, ['type' => 'OUT', 'quantity' => 10])->assertSessionHasNoErrors();

        $this->assertSame(40, $this->product->fresh()->quantity);
        $movement = StockMovement::sole();
        $this->assertSame([50, 40, 10], [$movement->previous_quantity, $movement->new_quantity, $movement->quantity]);
    }

    public function test_stock_out_greater_than_available_is_rejected_and_nothing_changes(): void
    {
        $this->record($this->staff, ['type' => 'OUT', 'quantity' => 51])->assertSessionHasErrors('quantity');

        $this->assertSame(50, $this->product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_stock_out_of_everything_reaches_exactly_zero(): void
    {
        $this->record($this->staff, ['type' => 'OUT', 'quantity' => 50])->assertSessionHasNoErrors();

        $this->assertSame(0, $this->product->fresh()->quantity);
    }

    public function test_quantity_must_be_a_positive_integer_for_in_and_out(): void
    {
        foreach ([0, -5, 2.5, 'abc'] as $bad) {
            $this->record($this->staff, ['type' => 'IN', 'quantity' => $bad])->assertSessionHasErrors('quantity');
        }

        $this->assertSame(0, StockMovement::count());
    }

    public function test_invalid_type_is_rejected(): void
    {
        $this->record($this->staff, ['type' => 'STEAL', 'quantity' => 1])->assertSessionHasErrors('type');
    }

    public function test_adjustment_requires_a_reason(): void
    {
        $this->record($this->staff, ['type' => 'ADJUSTMENT', 'quantity' => 45, 'reason' => ''])->assertSessionHasErrors('reason');

        $this->assertSame(50, $this->product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_adjustment_sets_counted_quantity_and_stores_absolute_difference(): void
    {
        $this->record($this->staff, ['type' => 'ADJUSTMENT', 'quantity' => 45, 'reason' => 'Stock count'])->assertSessionHasNoErrors();

        $this->assertSame(45, $this->product->fresh()->quantity);
        $movement = StockMovement::sole();
        $this->assertSame('ADJUSTMENT', $movement->type);
        $this->assertSame(5, $movement->quantity);
        $this->assertSame(-5, $movement->signedChange());
        $this->assertSame([50, 45], [$movement->previous_quantity, $movement->new_quantity]);
    }

    public function test_adjustment_to_zero_is_allowed_but_negative_is_not(): void
    {
        $this->record($this->staff, ['type' => 'ADJUSTMENT', 'quantity' => -1, 'reason' => 'x'])->assertSessionHasErrors('quantity');
        $this->record($this->staff, ['type' => 'ADJUSTMENT', 'quantity' => 0, 'reason' => 'Damaged'])->assertSessionHasNoErrors();

        $this->assertSame(0, $this->product->fresh()->quantity);
    }

    public function test_adjustment_that_changes_nothing_is_rejected(): void
    {
        $this->record($this->staff, ['type' => 'ADJUSTMENT', 'quantity' => 50, 'reason' => 'Recount'])->assertSessionHasErrors('quantity');

        $this->assertSame(0, StockMovement::count());
    }

    public function test_client_supplied_user_and_quantities_are_ignored(): void
    {
        $this->record($this->staff, [
            'type' => 'IN', 'quantity' => 5,
            'user_id' => $this->otherStaff->id, 'previous_quantity' => 999, 'new_quantity' => 999,
        ])->assertSessionHasNoErrors();

        $movement = StockMovement::sole();
        $this->assertSame($this->staff->id, $movement->user_id);
        $this->assertSame([50, 55], [$movement->previous_quantity, $movement->new_quantity]);
    }

    // ---------- Authorization ----------

    public function test_staff_cannot_record_movements_for_another_users_product(): void
    {
        $this->record($this->otherStaff, ['type' => 'OUT', 'quantity' => 10])->assertForbidden();

        $this->assertSame(50, $this->product->fresh()->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_admin_can_record_movements_for_any_product(): void
    {
        $this->record($this->admin, ['type' => 'IN', 'quantity' => 1], 'admin')->assertSessionHasNoErrors();

        $this->assertSame(51, $this->product->fresh()->quantity);
        $this->assertSame($this->admin->id, StockMovement::sole()->user_id);
    }

    public function test_archived_products_cannot_receive_movements(): void
    {
        $this->product->forceFill(['quantity' => 0])->save();
        $this->product->delete();

        $this->record($this->staff, ['type' => 'IN', 'quantity' => 5])->assertSessionHasErrors('product_id');
        $this->assertSame(0, StockMovement::count());
    }

    public function test_staff_history_shows_only_movements_for_own_products(): void
    {
        $foreign = Product::factory()->ownedBy($this->otherStaff)->quantity(10)->create(['name' => 'Foreign Thing']);
        app(StockService::class)->stockIn($foreign, $this->otherStaff, 3);
        app(StockService::class)->stockIn($this->product, $this->staff, 2);

        $response = $this->actingAs($this->staff)->get('/staff/stock-movements')->assertOk();
        $this->assertSame(1, $response->viewData('movements')->total());
        $response->assertDontSee('Foreign Thing');

        $this->assertSame(2, $this->actingAs($this->admin)->get('/admin/stock-movements')->viewData('movements')->total());
    }

    public function test_staff_cannot_read_foreign_history_by_crafting_product_id_filter(): void
    {
        $foreign = Product::factory()->ownedBy($this->otherStaff)->quantity(0)->create(['name' => 'Secret Foreign Product']);
        app(StockService::class)->stockIn($foreign, $this->otherStaff, 7);

        $response = $this->actingAs($this->staff)->get("/staff/stock-movements?product_id={$foreign->id}")->assertOk();

        $this->assertSame(0, $response->viewData('movements')->total());
        $this->assertNull($response->viewData('filteredProduct'));
        $response->assertDontSee('Secret Foreign Product');
    }

    public function test_staff_create_form_lists_only_own_products(): void
    {
        Product::factory()->ownedBy($this->otherStaff)->create(['name' => 'Not Mine']);

        $this->actingAs($this->staff)->get('/staff/stock-movements/create')
            ->assertOk()->assertSee($this->product->name)->assertDontSee('Not Mine');
    }

    public function test_movement_history_filters_by_type_and_product(): void
    {
        $service = app(StockService::class);
        $service->stockIn($this->product, $this->staff, 5);
        $service->stockOut($this->product, $this->staff, 2);

        $this->assertSame(1, $this->actingAs($this->staff)->get('/staff/stock-movements?type=OUT')->viewData('movements')->total());
        $this->assertSame(2, $this->actingAs($this->staff)->get("/staff/stock-movements?product_id={$this->product->id}")->viewData('movements')->total());
    }

    public function test_movements_cannot_be_edited_or_deleted(): void
    {
        $movement = app(StockService::class)->stockIn($this->product, $this->staff, 5);

        // No edit/update/destroy routes exist (404 or 405 depending on URI matching).
        $this->actingAs($this->admin)->get("/admin/stock-movements/{$movement->id}/edit")->assertNotFound();
        $this->assertContains($this->actingAs($this->admin)->put("/admin/stock-movements/{$movement->id}", ['quantity' => 1])->status(), [404, 405]);
        $this->assertContains($this->actingAs($this->admin)->delete("/admin/stock-movements/{$movement->id}")->status(), [404, 405]);

        $this->assertModelExists($movement);
        $this->assertSame(5, $movement->fresh()->quantity);
    }

    public function test_movement_history_survives_product_archival(): void
    {
        $service = app(StockService::class);
        $service->stockOut($this->product, $this->staff, 50);
        $this->product->delete();

        $this->actingAs($this->staff)->get('/staff/stock-movements')
            ->assertOk()->assertSee($this->product->name)->assertSee('Archived');
    }

    // ---------- Service-level guarantees ----------

    public function test_failed_movement_insert_rolls_back_the_quantity_update(): void
    {
        StockMovement::creating(function () {
            throw new RuntimeException('Simulated failure while writing the audit record');
        });

        try {
            app(StockService::class)->stockIn($this->product, $this->staff, 10);
            $this->fail('Expected the simulated failure.');
        } catch (RuntimeException $e) {
            $this->assertSame('Simulated failure while writing the audit record', $e->getMessage());
        }

        $this->assertSame(50, Product::find($this->product->id)->quantity);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_service_rejects_unauthorized_user_even_if_controller_is_bypassed(): void
    {
        $this->expectException(AuthorizationException::class);

        app(StockService::class)->stockOut($this->product, $this->otherStaff, 1);
    }

    public function test_service_rejects_adjustment_without_reason(): void
    {
        $this->expectException(StockException::class);

        app(StockService::class)->record($this->product, $this->staff, StockMovement::TYPE_ADJUSTMENT, 10, '   ');
    }

    public function test_service_never_allows_negative_stock(): void
    {
        try {
            app(StockService::class)->stockOut($this->product, $this->staff, 1000);
            $this->fail('Expected StockException');
        } catch (StockException) {
            // expected
        }

        $this->assertSame(50, $this->product->fresh()->quantity);
    }

    public function test_quantity_always_matches_the_movement_ledger(): void
    {
        $service = app(StockService::class);
        $product = Product::factory()->ownedBy($this->staff)->quantity(0)->create();

        $service->stockIn($product, $this->staff, 20);
        $service->stockOut($product, $this->staff, 10);
        $service->adjust($product, $this->staff, 55, 'Recount');
        $service->stockOut($product, $this->staff, 5);

        $ledger = StockMovement::where('product_id', $product->id)->get()->sum(fn ($m) => $m->signedChange());
        $this->assertSame(50, $product->fresh()->quantity);
        $this->assertSame(50, $ledger);
    }
}
