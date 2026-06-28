<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Enums\RoleEnum;

class SecurityRuntimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\PermissionSeeder::class, \Database\Seeders\RolePermissionSeeder::class]);
    }

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_login_success()
    {
        $role = Role::where('role_name', RoleEnum::KETUA_RW->value)->first();
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'password' => bcrypt('password123'),
            'status' => 'ACTIVE'
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_failure_invalid_credentials()
    {
        $role = Role::where('role_name', RoleEnum::KETUA_RW->value)->first();
        $user = User::factory()->create([
            'role_id' => $role->role_id,
            'password' => bcrypt('password123'),
            'status' => 'ACTIVE'
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_warga_role_cannot_access_admin_features()
    {
        $role = Role::where('role_name', RoleEnum::WARGA->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id, 'status' => 'ACTIVE']);
        
        $this->actingAs($user);

        // Access Dashboard (authenticated)
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Access Warga (Gate will block since Warga role doesn't have view_residents)
        $response = $this->get(route('warga.index'));
        $response->assertStatus(403);
        
        // Access Finance
        $response = $this->get(route('finances.dashboard'));
        $response->assertStatus(403);
    }

    public function test_ketua_rt_authorization()
    {
        $role = Role::where('role_name', RoleEnum::KETUA_RT->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id, 'status' => 'ACTIVE']);
        
        $this->actingAs($user);

        // Ketua RT can view residents
        $response = $this->get(route('warga.index'));
        $response->assertStatus(200);

        // Ketua RT can view complaints
        $response = $this->get(route('complaints.index'));
        $response->assertStatus(200);

        // Ketua RT CANNOT manage system / Super Admin routes
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_has_all_access()
    {
        $role = Role::where('role_name', RoleEnum::SUPER_ADMIN->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id, 'status' => 'ACTIVE']);
        
        $this->actingAs($user);

        // Should access system routes
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
        
        // Should access all other routes because of Gate::before
        $response = $this->get(route('warga.index'));
        $response->assertStatus(200);
    }
}
