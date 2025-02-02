<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'patient', 'doctor'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $adminEmail = 'admin@gmail.com';

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'admin',
                'password' => '123456789', // قم بتعيين كلمة مرور صحيحة
            ]
        );

        // تعيين دور الأدمن
        $admin->assignRole('admin');
    }
}
