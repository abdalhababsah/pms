<?php

namespace Database\Factories;

use App\Models\WorkDay;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkDayFactory extends Factory
{
    protected $model = WorkDay::class;

    public function definition(): array
    {
        // Ensure we pick an employee with role 3 (employee)
        $employeeId = User::inRandomOrder()->where('role_id', 3)->value('id');
        if (!$employeeId) {
            // If none exists, create one with role 3.
            $employeeId = User::factory()->create(['role_id' => 3])->id;
        }
        
        // Try to get an existing shift or create one.
        $shiftId = Shift::inRandomOrder()->whereIn('id', [1,2, 3])->value('id') ?? Shift::factory()->create()->id;
        
        return [
            'employee_id' => $employeeId,
            // Generate a random date in the past year
            'date'        => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'shift_id'    => $shiftId,
            'location'    => $this->faker->randomElement(['Office', 'Work from home']),
        ];
    }
}