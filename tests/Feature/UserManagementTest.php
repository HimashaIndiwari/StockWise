<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create(['name' => 'Main Admin']);
        $this->staff = User::factory()->staff()->create(['name' => 'Staff Person']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'New Staff',
            'email' => 'New.Staff@Example.com',
            'role' => 'staff',
            'status' => 'active',
            'password' => 'Secret-pass-123',
            'password_confirmation' => 'Secret-pass-123',
        ], $overrides);
    }

    public function test_admin_can_list_search_and_view_users_without_password_hashes(): void
    {
        $this->actingAs($this->admin)->get('/admin/users')
            ->assertOk()->assertSee('Staff Person')->assertDontSee($this->staff->password);

        $found = $this->actingAs($this->admin)->get('/admin/users?q=Staff%20Person')->assertOk()->viewData('users');
        $this->assertSame([$this->staff->id], $found->pluck('id')->all());

        $this->assertSame(1, $this->actingAs($this->admin)->get('/admin/users?role=admin')->viewData('users')->total());

        $this->actingAs($this->admin)->get("/admin/users/{$this->staff->id}")
            ->assertOk()->assertSee($this->staff->email)->assertDontSee($this->staff->password);
    }

    public function test_admin_can_create_a_staff_user(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', $this->payload())->assertSessionHasNoErrors();

        $user = User::firstWhere('email', 'new.staff@example.com');
        $this->assertNotNull($user);
        $this->assertSame('staff', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertTrue(Hash::check('Secret-pass-123', $user->password));
    }

    public function test_user_creation_is_validated(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', $this->payload([
            'name' => '', 'email' => $this->staff->email, 'role' => 'superuser', 'status' => 'maybe',
            'password' => 'short', 'password_confirmation' => 'different',
        ]))->assertSessionHasErrors(['name', 'email', 'role', 'status', 'password']);
    }

    public function test_admin_can_update_user_and_keep_password_when_blank(): void
    {
        $oldHash = $this->staff->password;

        $this->actingAs($this->admin)->put("/admin/users/{$this->staff->id}", $this->payload([
            'name' => 'Renamed', 'email' => $this->staff->email, 'password' => '', 'password_confirmation' => '',
        ]))->assertSessionHasNoErrors();

        $this->staff->refresh();
        $this->assertSame('Renamed', $this->staff->name);
        $this->assertSame($oldHash, $this->staff->password);
    }

    public function test_admin_can_promote_and_deactivate_staff(): void
    {
        $this->actingAs($this->admin)->put("/admin/users/{$this->staff->id}", $this->payload([
            'email' => $this->staff->email, 'role' => 'admin', 'password' => '', 'password_confirmation' => '',
        ]))->assertSessionHasNoErrors();
        $this->assertTrue($this->staff->fresh()->isAdmin());

        $other = User::factory()->staff()->create();
        $this->actingAs($this->admin)->patch("/admin/users/{$other->id}/status")->assertSessionHas('success');
        $this->assertFalse($other->fresh()->isActive());

        $this->actingAs($this->admin)->patch("/admin/users/{$other->id}/status")->assertSessionHas('success');
        $this->assertTrue($other->fresh()->isActive());
    }

    public function test_admin_cannot_demote_or_deactivate_themselves(): void
    {
        $this->actingAs($this->admin)->put("/admin/users/{$this->admin->id}", $this->payload([
            'email' => $this->admin->email, 'role' => 'staff', 'password' => '', 'password_confirmation' => '',
        ]))->assertSessionHasErrors('role');

        $this->actingAs($this->admin)->patch("/admin/users/{$this->admin->id}/status")->assertSessionHas('error');

        $this->admin->refresh();
        $this->assertTrue($this->admin->isAdmin());
        $this->assertTrue($this->admin->isActive());
    }

    public function test_another_admin_can_be_deactivated_while_an_active_admin_remains(): void
    {
        $second = User::factory()->admin()->create();

        $this->actingAs($this->admin)->patch("/admin/users/{$second->id}/status")->assertSessionHas('success');

        $this->assertFalse($second->fresh()->isActive());
        $this->assertSame(1, User::activeAdmins()->count());
    }

    public function test_the_last_active_admin_always_remains(): void
    {
        // Two active admins: one demotes the other — allowed.
        $second = User::factory()->admin()->create();
        $this->actingAs($second)->put("/admin/users/{$this->admin->id}", $this->payload([
            'email' => $this->admin->email, 'role' => 'staff', 'password' => '', 'password_confirmation' => '',
        ]))->assertSessionHasNoErrors();
        $this->assertSame(1, User::activeAdmins()->count());

        // $second is now the last active admin: neither demotion nor deactivation can remove it.
        $this->actingAs($second)->put("/admin/users/{$second->id}", $this->payload([
            'email' => $second->email, 'role' => 'staff', 'password' => '', 'password_confirmation' => '',
        ]))->assertSessionHasErrors('role');
        $this->actingAs($second)->put("/admin/users/{$second->id}", $this->payload([
            'email' => $second->email, 'role' => 'admin', 'status' => 'inactive', 'password' => '', 'password_confirmation' => '',
        ]))->assertSessionHasErrors('role');
        $this->actingAs($second)->patch("/admin/users/{$second->id}/status")->assertSessionHas('error');

        $this->assertSame(1, User::activeAdmins()->count());
        $this->assertTrue($second->fresh()->isAdmin() && $second->fresh()->isActive());
    }

    public function test_staff_cannot_access_user_management_or_assign_roles(): void
    {
        $this->actingAs($this->staff)->get('/admin/users')->assertForbidden();
        $this->actingAs($this->staff)->post('/admin/users', $this->payload(['role' => 'admin']))->assertForbidden();
        $this->actingAs($this->staff)->put("/admin/users/{$this->staff->id}", $this->payload(['email' => $this->staff->email, 'role' => 'admin']))->assertForbidden();
        $this->actingAs($this->staff)->patch("/admin/users/{$this->admin->id}/status")->assertForbidden();

        $this->assertFalse($this->staff->fresh()->isAdmin());
        $this->assertTrue($this->admin->fresh()->isActive());
        $this->assertDatabaseMissing('users', ['email' => 'new.staff@example.com']);
    }

    public function test_users_cannot_be_hard_deleted(): void
    {
        $this->assertContains($this->actingAs($this->admin)->delete("/admin/users/{$this->staff->id}")->status(), [404, 405]);
        $this->assertModelExists($this->staff);
    }
}
