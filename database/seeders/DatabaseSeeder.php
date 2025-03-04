<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            TeamLeadersSeeder::class,  // if using separate seeders for team leaders
            EmployeesSeeder::class,    // if using separate seeders for employees
            ProjectsSeeder::class,
            FeedbackSeeder::class,
            WorkDaySeeder::class,
            WorkLogSeeder::class,
        ]);
    }
}