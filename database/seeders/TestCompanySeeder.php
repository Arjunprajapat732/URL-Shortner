<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test company
        $company = Company::create([
            'name' => 'Test Company',
            'email' => 'testcompany@example.com',
        ]);

        // Create an Admin user for the company
        User::create([
            'name' => 'Company Admin',
            'email' => 'admin@testcompany.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        // Create a Member user for the company
        User::create([
            'name' => 'Company Member',
            'email' => 'member@testcompany.com',
            'password' => Hash::make('password'),
            'role' => 'Member',
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);
    }
}
