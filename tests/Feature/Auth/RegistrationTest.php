<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'New Staff',
            'email' => 'new.staff@example.com',
            'password' => 'Secret-pass-123',
            'password_confirmation' => 'Secret-pass-123',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered_and_is_linked_from_login(): void
    {
        $this->get('/register')->assertOk()->assertSee('Create account');
        $this->get('/login')->assertOk()->assertSee(route('register'));
    }

    public function test_new_user_is_registered_as_active_staff_and_sent_to_staff_dashboard(): void
    {
        $this->post('/register', $this->validData())
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $user = User::where('email', 'new.staff@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_STAFF, $user->role);
        $this->assertSame(User::STATUS_ACTIVE, $user->status);
        $this->assertNotSame('Secret-pass-123', $user->password);
        $this->assertTrue(Hash::check('Secret-pass-123', $user->password));

        $this->get('/dashboard')->assertRedirect(route('staff.dashboard'));
        $this->get('/admin/dashboard')->assertForbidden();
    }

    public function test_registration_cannot_assign_admin_role_or_inactive_status(): void
    {
        $this->post('/register', $this->validData(['role' => 'admin', 'status' => 'inactive']));

        $user = User::where('email', 'new.staff@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_STAFF, $user->role);
        $this->assertSame(User::STATUS_ACTIVE, $user->status);
    }

    public function test_registration_validates_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post('/register', $this->validData(['email' => 'TAKEN@example.com']))
            ->assertSessionHasErrors('email');
        $this->post('/register', $this->validData(['password_confirmation' => 'different']))
            ->assertSessionHasErrors('password');
        $this->post('/register', ['name' => '', 'email' => 'not-an-email', 'password' => ''])
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertGuest();
        $this->assertSame(1, User::count());
    }

    public function test_authenticated_users_cannot_access_registration(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/register')->assertRedirect();
    }
}
