<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
        ['email' => 'admin@upi.test'],
        ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
    );

    User::updateOrCreate(
        ['email' => 'operator@upi.test'],
        ['name' => 'Operator', 'password' => Hash::make('password'), 'role' => 'operator']
    );

    User::updateOrCreate(
        ['email' => 'wadek@upi.test'],
        ['name' => 'Wadek', 'password' => Hash::make('password'), 'role' => 'wadek']
    );
    }
}
