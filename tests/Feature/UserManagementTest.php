<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_admin_can_view_user_list(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Quản lý Người dùng');
    }

    public function test_non_admin_cannot_view_user_list(): void
    {
        $patient = User::where('role', User::ROLE_PATIENT)->first();

        $response = $this->actingAs($patient)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_a_new_user(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $email = 'testuser_' . rand(1000, 9999) . '@tamanh.vn';

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Nhân viên Mới',
            'email' => $email,
            'password' => 'secret123',
            'phone' => '0911223344',
            'role' => User::ROLE_RECEPTIONIST,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => User::ROLE_RECEPTIONIST,
        ]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->delete('/admin/users/' . $admin->id);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
