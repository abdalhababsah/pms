<?php

namespace Database\Factories;

use App\Models\WorkLog;
use App\Models\WorkDay;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkLogFactory extends Factory
{
    protected $model = WorkLog::class;

    public function definition(): array
    {
        // Get a random WorkDay record for an employee; if none exists, create one.
        $workDay = WorkDay::inRandomOrder()->first() ?? WorkDay::factory()->create();
        
        // For a given work day, define a start time between 8 AM and 12 PM of that day.
        $workDayDate = $workDay->date;
        $startTime = $this->faker->dateTimeBetween($workDayDate . ' 08:00:00', $workDayDate . ' 12:00:00');
        // Define an end time between 1 and 4 hours after start.
        $endTime = (clone $startTime)->modify('+' . $this->faker->numberBetween(1, 4) . ' hours');
        
        // Get an existing project or create one if needed.
        $projectId = Project::inRandomOrder()->value('id') ?? \App\Models\Project::factory()->create()->id;
        
        return [
            'work_day_id'      => $workDay->id,
            'project_id'       => $projectId,
            'task_count'       => $this->faker->numberBetween(1, 10),
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'task_description' => $this->faker->sentence(10),
        ];
    }
}