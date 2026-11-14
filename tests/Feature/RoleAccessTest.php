<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_protected_pages(): void
    {
        foreach (['/dashboard', '/admin/dashboard', '/staff/dashboard', '/profile'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->get('/dashboard')->assertRedirect(route('admin.dashboard'));
        $this->get('/admin/dashboard')->assertOk()->assertSee('Admin Dashboard');
    }

    public function test_staff_login_redirects_to_staff_dashboard(): void
    {
        $staff = User::factory()->staff()->create();

        $this->post('/login', ['email' => $staff->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->get('/dashboard')->assertRedirect(route('staff.dashboard'));
        $this->get('/staff/dashboard')->assertOk()->assertSee('My Dashboard');
    }

    public function test_staff_cannot_access_admin_routes(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admin_cannot_use_staff_area_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/staff/dashboard')->assertForbidden();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_deactivated_during_session_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->staff()->create();
        $this->actingAs($user)->get('/staff/dashboard')->assertOk();

        $user->status = User::STATUS_INACTIVE;
        $user->save();

        $this->get('/staff/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'intruder@example.com']);
    }

    public function test_staff_navigation_does_not_show_admin_links(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get('/staff/dashboard')
            ->assertOk()
            ->assertDontSee('User Management')
            ->assertDontSee(route('admin.dashboard'));
    }

    public function test_staff_dashboard_counts_only_own_products(): void
    {
        $staff = User::factory()->staff()->create();
        $other = User::factory()->staff()->create();
        Product::factory()->ownedBy($staff)->quantity(0)->create();
        Product::factory()->ownedBy($other)->quantity(0)->count(3)->create();

        $response = $this->actingAs($staff)->get('/staff/dashboard')->assertOk();

        $this->assertSame(1, $response->viewData('stats')['products']);
        $this->assertSame(1, $response->viewData('stats')['out_of_stock']);
    }

    public function test_admin_dashboard_counts_all_products(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(4)->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard')->assertOk();

        $this->assertSame(4, $response->viewData('stats')['products']);
    }
}
