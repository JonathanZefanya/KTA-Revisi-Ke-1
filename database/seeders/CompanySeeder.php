<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Create an admin user if not exists
        $admin = Admin::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('Admin!1234')
        ]);
    }
}
