<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TeamLeadersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->state([
            'role_id' => 2, // Team Leader
            'password' => Hash::make('password'),
        ])->create()->each(function ($user) {
            // Override email to always end with @menadevs.io
            // Using a slug of the user's first and last name plus a random number for uniqueness.
            $slug = Str::slug($user->first_name . '.' . $user->last_name);
            $user->email = $slug . rand(100, 999) . '@menadevs.io';
            $user->save();
        });
    }
}