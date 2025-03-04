<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkLog;

class WorkLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 30,000 WorkLog records. Adjust the count as needed.
        WorkLog::factory()->count(10000)->create();
    }
}