<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@gurukul.edu'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Change in production
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );
    }
}
