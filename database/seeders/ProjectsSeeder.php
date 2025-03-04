<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 projects (adjust count as needed)
        Project::factory()->count(30)->create();
    }
}