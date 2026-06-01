<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Standar Admin
        User::updateOrCreate(
            ['email' => 'admin@stoku.com'],
            [
                'name' => 'Administrator Stoku',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        );

        // 2. Akun Standar Kasir
        User::updateOrCreate(
            ['email' => 'kasir@stoku.com'],
            [
                'name' => 'Siti Kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir'
            ]
        );
    }
}