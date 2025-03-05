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
            StatusSeeder::class,
            TeamLeadersSeeder::class,  
            EmployeesSeeder::class,    
            ProjectsSeeder::class,
            FeedbackSeeder::class,
            ShiftSeeder::class,
            WorkDaySeeder::class,
            WorkLogSeeder::class,
        ]);
    }
}