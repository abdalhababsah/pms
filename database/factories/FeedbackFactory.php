<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        // Get a random employee (role 3)
        $employee = User::where('role_id', 3)->inRandomOrder()->first();
        if (!$employee) {
            // If no employee exists, create one with role 3.
            $employee = User::factory()->create(['role_id' => 3]);
        }
        
        // Get a random project
        $project = Project::inRandomOrder()->first();
        if (!$project) {
            // If no project exists, create one.
            $project = Project::factory()->create();
        }
        
        return [
            'employee_id'   => $employee->id,
            'project_id'    => $project->id,
            'rating'        => $this->faker->numberBetween(1, 5),
            'justification' => $this->faker->paragraph(),
            'created_at'    => $this->faker->dateTimeBetween(now()),
            'updated_at'    => now(),
        ];
    }
}