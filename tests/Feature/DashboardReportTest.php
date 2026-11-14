<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    private User $otherStaff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
        $this->otherStaff = User::factory()->staff()->create();
    }

    private function seedInventory(): void
    {
        $stock = app(StockService::class);
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        $make = fn (User $owner, int $min) => Product::factory()->ownedBy($owner)->quantity(0)
            ->create(['category_id' => $category->id, 'supplier_id' => $supplier->id, 'min_stock_level' => $min]);

        // staff: in stock (20), low (3 <= 5), out (0)
        $stock->stockIn($make($this->staff, 5), $this->staff, 20);
        $stock->stockIn($make($this->staff, 5), $this->staff, 3);
        $make($this->staff, 5);
        // other staff: low (2), out (0)
        $stock->stockIn($make($this->otherStaff, 5), $this->otherStaff, 2);
        $make($this->otherStaff, 5);
    }

    public function test_admin_dashboard_figures_are_database_driven(): void
    {
        $this->seedInventory();

        $stats = $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk()->viewData('stats');

        $this->assertSame([
            'products' => 5, 'categories' => 1, 'suppliers' => 1, 'users' => 3,
            'stock_quantity' => 25, 'low_stock' => 2, 'out_of_stock' => 2,
        ], $stats);
    }

    public function test_staff_dashboard_figures_cover_only_own_products(): void
    {
        $this->seedInventory();

        $response = $this->actingAs($this->staff)->get('/staff/dashboard')->assertOk();

        $this->assertSame(['products' => 3, 'stock_quantity' => 23, 'low_stock' => 1, 'out_of_stock' => 1], $response->viewData('stats'));
        $this->assertTrue($response->viewData('recentMovements')->every(fn ($m) => $m->product->created_by === $this->staff->id));
        $this->assertTrue($response->viewData('attentionProducts')->every(fn ($p) => $p->created_by === $this->staff->id));
    }

    public function test_dashboards_render_empty_states_without_data(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk()->assertSee('No stock movements yet');
        $this->actingAs($this->staff)->get('/staff/dashboard')->assertOk()->assertSee('All stock levels are healthy');
    }

    public function test_admin_report_summarises_movements_and_stock_alerts(): void
    {
        $this->seedInventory();
        $product = Product::where('created_by', $this->staff->id)->where('quantity', 20)->first();
        $stock = app(StockService::class);
        $stock->stockOut($product, $this->staff, 4);
        $stock->adjust($product, $this->admin, 10, 'Recount');   // 16 -> 10 (down 6)

        $response = $this->actingAs($this->admin)->get('/admin/reports')->assertOk();
        $summary = $response->viewData('summary');

        $this->assertSame(25, $summary['in_units']);
        $this->assertSame(4, $summary['out_units']);
        $this->assertSame(6, $summary['adjust_down']);
        $this->assertSame(15, $summary['net_change']);
        $this->assertSame(2, $response->viewData('lowStock')->count());
        $this->assertSame(2, $response->viewData('outOfStock')->count());
        $this->assertSame(15, $response->viewData('inventory')['units']);
    }

    public function test_report_date_range_is_validated_and_applied(): void
    {
        $this->seedInventory();
        StockMovement::query()->update(['created_at' => now()->subDays(60)]);

        $summary = $this->actingAs($this->admin)->get('/admin/reports')->viewData('summary');
        $this->assertSame(0, $summary['in_units']);

        $from = now()->subDays(90)->format('Y-m-d');
        $summary = $this->actingAs($this->admin)->get("/admin/reports?from={$from}")->viewData('summary');
        $this->assertSame(25, $summary['in_units']);

        $this->actingAs($this->admin)->get('/admin/reports?from=2026-02-10&to=2026-01-01')->assertSessionHasErrors('to');
    }

    public function test_staff_cannot_open_reports(): void
    {
        $this->actingAs($this->staff)->get('/admin/reports')->assertForbidden();
    }

    public function test_every_sidebar_link_renders_and_resolves_for_each_role(): void
    {
        $adminLinks = ['admin.dashboard', 'admin.products.index', 'admin.categories.index', 'admin.suppliers.index',
            'admin.stock-movements.index', 'admin.users.index', 'admin.reports.index', 'profile.edit'];
        $staffLinks = ['staff.dashboard', 'staff.products.index', 'staff.stock-movements.index', 'profile.edit'];

        $page = $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();
        foreach ($adminLinks as $name) {
            $page->assertSee('href="'.route($name).'"', false);
            $this->actingAs($this->admin)->get(route($name))->assertOk();
        }

        $page = $this->actingAs($this->staff)->get('/staff/dashboard')->assertOk();
        foreach ($staffLinks as $name) {
            $page->assertSee('href="'.route($name).'"', false);
            $this->actingAs($this->staff)->get(route($name))->assertOk();
        }
        $page->assertDontSee('href="'.route('admin.users.index').'"', false);
    }
}
