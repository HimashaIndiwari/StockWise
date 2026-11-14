<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySupplierTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
    }

    // ---------- Categories ----------

    public function test_admin_can_create_list_update_and_delete_a_category(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/categories', ['name' => '  Garden   Tools ', 'description' => 'Outdoor'])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::firstWhere('name', 'Garden Tools');
        $this->assertNotNull($category, 'Whitespace should be normalised');

        $this->actingAs($this->admin)->get('/admin/categories')->assertOk()->assertSee('Garden Tools');
        $this->actingAs($this->admin)->get("/admin/categories/{$category->id}")->assertOk();

        $this->actingAs($this->admin)
            ->put("/admin/categories/{$category->id}", ['name' => 'Garden', 'description' => null])
            ->assertSessionHasNoErrors();
        $this->assertSame('Garden', $category->fresh()->name);

        $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}")
            ->assertRedirect(route('admin.categories.index'));
        $this->assertModelMissing($category);
    }

    public function test_category_name_is_required_and_unique(): void
    {
        Category::factory()->create(['name' => 'Electronics']);

        $this->actingAs($this->admin)->post('/admin/categories', ['name' => ''])->assertSessionHasErrors('name');
        $this->actingAs($this->admin)->post('/admin/categories', ['name' => 'Electronics'])->assertSessionHasErrors('name');
    }

    public function test_category_can_keep_its_own_name_on_update(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);

        $this->actingAs($this->admin)
            ->put("/admin/categories/{$category->id}", ['name' => 'Electronics', 'description' => 'Updated'])
            ->assertSessionHasNoErrors();
    }

    public function test_category_in_use_cannot_be_deleted_even_by_archived_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'quantity' => 0]);
        $product->delete(); // archived product still references the category

        $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}")->assertSessionHas('error');

        $this->assertModelExists($category);
    }

    public function test_category_search_and_pagination(): void
    {
        Category::factory()->create(['name' => 'Kitchenware']);
        Category::factory()->count(12)->sequence(fn ($s) => ['name' => 'Bulk Cat '.$s->index])->create();

        $this->actingAs($this->admin)->get('/admin/categories?q=Kitchen')
            ->assertOk()->assertSee('Kitchenware')->assertDontSee('Bulk Cat 1');

        $response = $this->actingAs($this->admin)->get('/admin/categories?q=Bulk');
        $this->assertSame(12, $response->viewData('categories')->total());
        $response->assertSee('q=Bulk', false);
    }

    // ---------- Suppliers ----------

    public function test_admin_can_create_update_and_delete_a_supplier(): void
    {
        $this->actingAs($this->admin)->post('/admin/suppliers', [
            'name' => 'Acme Ltd',
            'email' => 'sales@acme.example',
            'phone' => '+94 11 234 5678',
            'address' => 'Colombo',
        ])->assertSessionHasNoErrors();

        $supplier = Supplier::firstWhere('name', 'Acme Ltd');
        $this->assertSame('sales@acme.example', $supplier->email);

        $this->actingAs($this->admin)->get("/admin/suppliers/{$supplier->id}")->assertOk()->assertSee('Acme Ltd');

        $this->actingAs($this->admin)
            ->put("/admin/suppliers/{$supplier->id}", ['name' => 'Acme Holdings', 'email' => '', 'phone' => '', 'address' => ''])
            ->assertSessionHasNoErrors();
        $supplier->refresh();
        $this->assertSame('Acme Holdings', $supplier->name);
        $this->assertNull($supplier->email);

        $this->actingAs($this->admin)->delete("/admin/suppliers/{$supplier->id}")->assertRedirect(route('admin.suppliers.index'));
        $this->assertModelMissing($supplier);
    }

    public function test_supplier_contact_details_are_validated(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/suppliers', ['name' => '', 'email' => 'not-an-email', 'phone' => 'call me maybe'])
            ->assertSessionHasErrors(['name', 'email', 'phone']);
    }

    public function test_supplier_in_use_cannot_be_deleted(): void
    {
        $supplier = Supplier::factory()->create();
        Product::factory()->create(['supplier_id' => $supplier->id]);

        $this->actingAs($this->admin)->delete("/admin/suppliers/{$supplier->id}")->assertSessionHas('error');

        $this->assertModelExists($supplier);
    }

    public function test_supplier_search_matches_name_email_and_phone(): void
    {
        Supplier::factory()->create(['name' => 'Alpha', 'email' => 'x@alpha.example', 'phone' => '0111']);
        Supplier::factory()->create(['name' => 'Beta', 'email' => 'y@beta.example', 'phone' => '0222']);

        $this->actingAs($this->admin)->get('/admin/suppliers?q=beta.example')->assertSee('Beta')->assertDontSee('Alpha');
        $this->actingAs($this->admin)->get('/admin/suppliers?q=0111')->assertSee('Alpha')->assertDontSee('Beta');
    }

    // ---------- Authorization ----------

    public function test_staff_cannot_manage_categories_or_suppliers(): void
    {
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($this->staff)->get('/admin/categories')->assertForbidden();
        $this->actingAs($this->staff)->post('/admin/categories', ['name' => 'Hack'])->assertForbidden();
        $this->actingAs($this->staff)->delete("/admin/categories/{$category->id}")->assertForbidden();
        $this->actingAs($this->staff)->get('/admin/suppliers')->assertForbidden();
        $this->actingAs($this->staff)->put("/admin/suppliers/{$supplier->id}", ['name' => 'Hack'])->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Hack']);
        $this->assertModelExists($category);
        $this->assertNotSame('Hack', $supplier->fresh()->name);
    }

    public function test_staff_can_still_select_categories_and_suppliers_when_creating_products(): void
    {
        Category::factory()->create(['name' => 'Selectable Category']);
        Supplier::factory()->create(['name' => 'Selectable Supplier']);

        $this->actingAs($this->staff)->get('/staff/products/create')
            ->assertOk()->assertSee('Selectable Category')->assertSee('Selectable Supplier');
    }
}
