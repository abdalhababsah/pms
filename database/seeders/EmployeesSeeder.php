<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EmployeesSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(200)->state([
            'role_id' => 3, // Employee
            'password' => Hash::make('password'),
        ])->create()->each(function ($user) {
            // Override email to always end with @menadevs.io
            $slug = Str::slug($user->first_name . '.' . $user->last_name);
            $user->email = $slug . rand(100, 999) . '@menadevs.io';
            $user->save();
        });
    }
}