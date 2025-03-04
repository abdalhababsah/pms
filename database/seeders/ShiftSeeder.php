<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define an array of default shifts.
        $shifts = [
            [
                'name'       => 'Morning Shift',
                'start_time' => '08:00:00',
                'end_time'   => '12:00:00',
            ],
            [
                'name'       => 'Afternoon Shift',
                'start_time' => '13:00:00',
                'end_time'   => '17:00:00',
            ],
            [
                'name'       => 'Night Shift',
                'start_time' => '18:00:00',
                'end_time'   => '22:00:00',
            ],
        ];

        foreach ($shifts as $shift) {
            DB::table('shifts')->insert([
                'name'       => $shift['name'],
                'start_time' => $shift['start_time'],
                'end_time'   => $shift['end_time'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}