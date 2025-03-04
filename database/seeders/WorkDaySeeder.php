<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkDay;

class WorkDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1,000 WorkDay records. Adjust the count as needed.
        WorkDay::factory()->count(10000)->create();
    }
}