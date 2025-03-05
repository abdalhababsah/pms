<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statuses')->insert([
            [
                'name' => 'onboarding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'working normally',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Empty queue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add additional statuses here if needed
        ]);
    }
}