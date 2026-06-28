<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([\Database\Seeders\RoleSeeder::class, \Database\Seeders\PermissionSeeder::class, \Database\Seeders\RolePermissionSeeder::class]);
    }

    public function test_profile_page_is_displayed(): void
    {
        $role = Role::where('role_name', \App\Enums\RoleEnum::KETUA_RW->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $role = Role::where('role_name', \App\Enums\RoleEnum::KETUA_RW->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'full_name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->full_name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $role = Role::where('role_name', \App\Enums\RoleEnum::KETUA_RW->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'full_name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $role = Role::where('role_name', \App\Enums\RoleEnum::KETUA_RW->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNotNull($user->fresh()->deleted_at);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $role = Role::where('role_name', \App\Enums\RoleEnum::KETUA_RW->value)->first();
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNull($user->fresh()->deleted_at);
    }
}
