<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Administrator',
            'password' => bcrypt('Admin!1234'),
            'role' => 'superadmin',
        ]);
    }
}
