<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        // Create 300 feedback records.
        Feedback::factory()->count(30000)->create();
    }
}