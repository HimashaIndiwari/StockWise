<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    private User $otherStaff;

    private Category $category;

    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
        $this->otherStaff = User::factory()->staff()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Wireless Keyboard',
            'sku' => 'kb-001',
            'description' => 'Compact keyboard',
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'purchase_price' => '1500.50',
            'selling_price' => '2100.00',
            'quantity' => 25,
            'min_stock_level' => 5,
        ], $overrides);
    }

    // ---------- Create ----------

    public function test_staff_can_create_a_valid_product_and_initial_stock_is_audited(): void
    {
        $response = $this->actingAs($this->staff)->post('/staff/products', $this->validPayload());

        $product = Product::firstWhere('sku', 'KB-001');
        $this->assertNotNull($product, 'SKU should be normalised to upper case');
        $response->assertRedirect(route('staff.products.show', $product));

        $this->assertSame($this->staff->id, $product->created_by);
        $this->assertSame(25, $product->quantity);
        $this->assertSame('1500.50', $product->purchase_price);

        $movement = $product->stockMovements()->sole();
        $this->assertSame(StockMovement::TYPE_IN, $movement->type);
        $this->assertSame(0, $movement->previous_quantity);
        $this->assertSame(25, $movement->new_quantity);
        $this->assertSame($this->staff->id, $movement->user_id);
    }

    public function test_product_with_zero_initial_quantity_has_no_movement(): void
    {
        $this->actingAs($this->staff)->post('/staff/products', $this->validPayload(['quantity' => 0]));

        $product = Product::firstWhere('sku', 'KB-001');
        $this->assertSame(0, $product->quantity);
        $this->assertSame(0, $product->stockMovements()->count());
    }

    public function test_created_by_is_never_taken_from_input(): void
    {
        $this->actingAs($this->staff)->post('/staff/products', $this->validPayload(['created_by' => $this->otherStaff->id]));

        $this->assertSame($this->staff->id, Product::firstWhere('sku', 'KB-001')->created_by);
    }

    public function test_missing_required_fields_are_rejected(): void
    {
        $this->actingAs($this->staff)
            ->post('/staff/products', [])
            ->assertSessionHasErrors(['name', 'sku', 'category_id', 'purchase_price', 'selling_price', 'min_stock_level']);

        $this->assertSame(0, Product::count());
    }

    public function test_duplicate_sku_is_rejected_even_when_the_existing_product_is_archived(): void
    {
        $existing = Product::factory()->ownedBy($this->staff)->create(['sku' => 'KB-001', 'quantity' => 0]);
        $existing->delete();

        $this->actingAs($this->staff)
            ->post('/staff/products', $this->validPayload(['sku' => 'kb-001']))
            ->assertSessionHasErrors('sku');
    }

    public function test_invalid_category_and_supplier_are_rejected(): void
    {
        $this->actingAs($this->staff)
            ->post('/staff/products', $this->validPayload(['category_id' => 9999, 'supplier_id' => 9999]))
            ->assertSessionHasErrors(['category_id', 'supplier_id']);
    }

    public function test_invalid_numbers_are_rejected(): void
    {
        $this->actingAs($this->staff)
            ->post('/staff/products', $this->validPayload([
                'purchase_price' => '-1',
                'selling_price' => '12.345',
                'quantity' => '2.5',
                'min_stock_level' => '-3',
            ]))
            ->assertSessionHasErrors(['purchase_price', 'selling_price', 'quantity', 'min_stock_level']);
    }

    public function test_supplier_is_optional(): void
    {
        $this->actingAs($this->staff)
            ->post('/staff/products', $this->validPayload(['supplier_id' => '']))
            ->assertSessionHasNoErrors();

        $this->assertNull(Product::firstWhere('sku', 'KB-001')->supplier_id);
    }

    public function test_image_upload_is_stored_and_replaced_cleanly(): void
    {
        Storage::fake('public');

        $this->actingAs($this->staff)->post('/staff/products', $this->validPayload([
            'image' => UploadedFile::fake()->image('kb.png', 200, 200),
        ]))->assertSessionHasNoErrors();

        $product = Product::firstWhere('sku', 'KB-001');
        Storage::disk('public')->assertExists($product->image);
        $oldImage = $product->image;

        $this->actingAs($this->staff)->put("/staff/products/{$product->id}", $this->validPayload([
            'image' => UploadedFile::fake()->image('kb2.jpg', 200, 200),
        ]))->assertSessionHasNoErrors();

        $product->refresh();
        Storage::disk('public')->assertExists($product->image);
        Storage::disk('public')->assertMissing($oldImage);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->staff)
            ->post('/staff/products', $this->validPayload(['image' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrors('image');
    }

    // ---------- Read ----------

    public function test_admin_sees_all_products_and_staff_only_their_own(): void
    {
        Product::factory()->ownedBy($this->staff)->create(['name' => 'Staff One Item']);
        Product::factory()->ownedBy($this->otherStaff)->create(['name' => 'Other Staff Item']);

        $this->actingAs($this->admin)->get('/admin/products')
            ->assertOk()->assertSee('Staff One Item')->assertSee('Other Staff Item');

        $this->actingAs($this->staff)->get('/staff/products')
            ->assertOk()->assertSee('Staff One Item')->assertDontSee('Other Staff Item');
    }

    public function test_staff_cannot_view_edit_update_or_delete_another_users_product(): void
    {
        $foreign = Product::factory()->ownedBy($this->otherStaff)->create(['quantity' => 0]);

        $this->actingAs($this->staff)->get("/staff/products/{$foreign->id}")->assertForbidden();
        $this->actingAs($this->staff)->get("/staff/products/{$foreign->id}/edit")->assertForbidden();
        $this->actingAs($this->staff)->put("/staff/products/{$foreign->id}", $this->validPayload())->assertForbidden();
        $this->actingAs($this->staff)->delete("/staff/products/{$foreign->id}")->assertForbidden();

        $this->assertNotSoftDeleted($foreign);
        $this->assertNotSame('Wireless Keyboard', $foreign->fresh()->name);
    }

    public function test_staff_cannot_use_admin_product_routes(): void
    {
        $own = Product::factory()->ownedBy($this->staff)->create();

        $this->actingAs($this->staff)->get('/admin/products')->assertForbidden();
        $this->actingAs($this->staff)->get("/admin/products/{$own->id}")->assertForbidden();
    }

    public function test_admin_can_view_any_product_with_creator(): void
    {
        $product = Product::factory()->ownedBy($this->staff)->create();

        $this->actingAs($this->admin)->get("/admin/products/{$product->id}")
            ->assertOk()->assertSee($product->name)->assertSee($this->staff->name);
    }

    public function test_search_filters_and_pagination_preserve_query_string(): void
    {
        Product::factory()->ownedBy($this->staff)->create(['name' => 'Blue Widget', 'sku' => 'BW-1', 'quantity' => 0]);
        Product::factory()->ownedBy($this->staff)->create(['name' => 'Red Gadget', 'sku' => 'RG-1', 'quantity' => 50, 'min_stock_level' => 5]);
        Product::factory()->ownedBy($this->staff)->count(12)->create(['name' => 'Widget Bulk', 'quantity' => 50, 'min_stock_level' => 5]);

        $this->actingAs($this->staff)->get('/staff/products?q=RG-1')
            ->assertOk()->assertSee('Red Gadget')->assertDontSee('Blue Widget');

        $this->actingAs($this->staff)->get('/staff/products?status=out_of_stock')
            ->assertOk()->assertSee('Blue Widget')->assertDontSee('Red Gadget');

        $response = $this->actingAs($this->staff)->get('/staff/products?q=Widget&sort=name&direction=asc');
        $response->assertOk();
        $this->assertSame(13, $response->viewData('products')->total());
        $this->assertSame(10, $response->viewData('products')->count());
        $response->assertSee('q=Widget', false)->assertSee('page=2', false);
    }

    public function test_invalid_sort_column_is_rejected(): void
    {
        $this->actingAs($this->staff)->get('/staff/products?sort=password')->assertSessionHasErrors('sort');
    }

    // ---------- Update ----------

    public function test_owner_can_update_product_but_not_its_quantity(): void
    {
        $product = Product::factory()->ownedBy($this->staff)->create(['quantity' => 7, 'sku' => 'OLD-1']);

        $this->actingAs($this->staff)
            ->put("/staff/products/{$product->id}", $this->validPayload(['sku' => 'OLD-1', 'name' => 'Renamed', 'quantity' => 999]))
            ->assertRedirect(route('staff.products.show', $product))
            ->assertSessionHas('success');

        $product->refresh();
        $this->assertSame('Renamed', $product->name);
        $this->assertSame(7, $product->quantity);
        $this->assertSame(0, $product->stockMovements()->count());
    }

    public function test_invalid_update_is_rejected(): void
    {
        $product = Product::factory()->ownedBy($this->staff)->create();
        Product::factory()->create(['sku' => 'TAKEN-1']);

        $this->actingAs($this->staff)
            ->put("/staff/products/{$product->id}", $this->validPayload(['name' => '', 'sku' => 'TAKEN-1', 'selling_price' => 'abc']))
            ->assertSessionHasErrors(['name', 'sku', 'selling_price']);
    }

    public function test_admin_can_update_any_product(): void
    {
        $product = Product::factory()->ownedBy($this->staff)->create(['sku' => 'ADM-1']);

        $this->actingAs($this->admin)
            ->put("/admin/products/{$product->id}", $this->validPayload(['sku' => 'ADM-1', 'name' => 'Admin Edit']))
            ->assertSessionHasNoErrors();

        $this->assertSame('Admin Edit', $product->fresh()->name);
        $this->assertSame($this->staff->id, $product->fresh()->created_by);
    }

    // ---------- Delete (archive) ----------

    public function test_product_with_stock_cannot_be_archived(): void
    {
        $product = Product::factory()->ownedBy($this->staff)->create(['quantity' => 3]);

        $this->actingAs($this->staff)->delete("/staff/products/{$product->id}")->assertSessionHas('error');

        $this->assertNotSoftDeleted($product);
    }

    public function test_admin_can_list_and_restore_archived_products_but_staff_cannot(): void
    {
        $product = Product::factory()->ownedBy($this->staff)->create(['quantity' => 0, 'name' => 'Old Item']);
        $product->delete();

        $this->actingAs($this->staff)->get('/admin/products/archived')->assertForbidden();
        $this->actingAs($this->staff)->patch("/admin/products/{$product->id}/restore")->assertForbidden();

        $listed = $this->actingAs($this->admin)->get('/admin/products/archived')->assertOk()->viewData('products');
        $this->assertTrue($listed->contains('id', $product->id));

        $this->actingAs($this->admin)->patch("/admin/products/{$product->id}/restore")
            ->assertRedirect(route('admin.products.show', $product));
        $this->assertNotSoftDeleted($product);
    }

    public function test_owner_can_archive_zero_stock_product_and_history_is_preserved(): void
    {
        $product = Product::factory()->ownedBy($this->staff)->create(['quantity' => 0, 'name' => 'To Archive']);
        StockMovement::factory()->create(['product_id' => $product->id, 'user_id' => $this->staff->id]);

        $this->actingAs($this->staff)->delete("/staff/products/{$product->id}")
            ->assertRedirect(route('staff.products.index'));

        $this->assertSoftDeleted($product);
        $this->assertSame(1, StockMovement::where('product_id', $product->id)->count());

        $listed = $this->actingAs($this->staff)->get('/staff/products')->viewData('products');
        $this->assertFalse($listed->contains('id', $product->id));
        $this->actingAs($this->staff)->get("/staff/products/{$product->id}")->assertNotFound();
    }
}
