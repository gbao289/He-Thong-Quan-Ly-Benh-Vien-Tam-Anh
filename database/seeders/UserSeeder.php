<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('123456');

        $users = [
            // 1. Admin
            [
                'name' => 'Ban Giám Đốc Tâm Anh',
                'email' => 'admin@tamanh.vn',
                'password' => $defaultPassword,
                'phone' => '0901234567',
                'role' => User::ROLE_ADMIN,
            ],
            // 2. Receptionist
            [
                'name' => 'Lễ tân Nguyễn Thu Hằng',
                'email' => 'receptionist@tamanh.vn',
                'password' => $defaultPassword,
                'phone' => '0902345678',
                'role' => User::ROLE_RECEPTIONIST,
            ],
            // 3. Doctors
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'doctor@tamanh.vn',
                'password' => $defaultPassword,
                'phone' => '0903456789',
                'role' => User::ROLE_DOCTOR,
            ],
            [
                'name' => 'Trần Quốc Hùng',
                'email' => 'doctor.hung@tamanh.vn',
                'password' => $defaultPassword,
                'phone' => '0904567890',
                'role' => User::ROLE_DOCTOR,
            ],
            [
                'name' => 'Lê Thị Lan',
                'email' => 'doctor.lan@tamanh.vn',
                'password' => $defaultPassword,
                'phone' => '0905678901',
                'role' => User::ROLE_DOCTOR,
            ],
            [
                'name' => 'Hoàng Minh Đạt',
                'email' => 'doctor.dat@tamanh.vn',
                'password' => $defaultPassword,
                'phone' => '0906789012',
                'role' => User::ROLE_DOCTOR,
            ],
            // 4. Patients
            [
                'name' => 'Nguyễn Văn Bệnh Nhân',
                'email' => 'patient@gmail.com',
                'password' => $defaultPassword,
                'phone' => '0987654321',
                'role' => User::ROLE_PATIENT,
            ],
            [
                'name' => 'Vũ Thị Tuyết Mai',
                'email' => 'patient.mai@gmail.com',
                'password' => $defaultPassword,
                'phone' => '0978123456',
                'role' => User::ROLE_PATIENT,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
