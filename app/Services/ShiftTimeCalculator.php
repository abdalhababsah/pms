<?php
namespace App\Services;

use Carbon\Carbon;

class ShiftTimeCalculator
{
    public function calculate($shift)
    {
        $tz = config('app.timezone'); // ensure you use your app timezone, e.g., 'Asia/Amman'
        $now   = Carbon::now($tz);
        $today = Carbon::today($tz);

        // Extract only the time portions in the correct timezone.
        $shiftStartTime = Carbon::parse($shift->start_time, $tz)->format('H:i:s'); // e.g. "21:00:00"
        $shiftEndTime   = Carbon::parse($shift->end_time, $tz)->format('H:i:s');   // e.g. "01:36:35"

        // Default: assume shift does NOT cross midnight.
        $shiftStart = $today->copy()->setTimeFromTimeString($shiftStartTime);
        $shiftEnd   = $today->copy()->setTimeFromTimeString($shiftEndTime);

        // If the shift crosses midnight (start time > end time)
        if ($shiftStartTime > $shiftEndTime) {
            if ($now->format('H:i:s') < $shiftEndTime) {
                // Current time is early: shift started yesterday.
                $shiftStart = $today->copy()->subDay()->setTimeFromTimeString($shiftStartTime);
                $shiftEnd   = $today->copy()->setTimeFromTimeString($shiftEndTime);
            } else {
                // Otherwise, shift starts today and ends tomorrow.
                $shiftStart = $today->copy()->setTimeFromTimeString($shiftStartTime);
                $shiftEnd   = $today->copy()->addDay()->setTimeFromTimeString($shiftEndTime);
            }
        }

        $isWithinShift = $now->between($shiftStart, $shiftEnd);

        return [
            'shiftStart'    => $shiftStart,
            'shiftEnd'      => $shiftEnd,
            'isWithinShift' => $isWithinShift,
        ];
    }
}