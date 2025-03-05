<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class ShiftProgress extends Component
{
    public $my_shift;

    public function mount($my_shift)
    {
        $this->my_shift = $my_shift;
    }

    public function getProgressProperty()
    {
        $tz = config('app.timezone'); // e.g. 'Asia/Amman'
        $now   = Carbon::now($tz);
        $today = Carbon::today($tz);
    
        // Parse stored times in the correct timezone.
        $startTime = Carbon::parse($this->my_shift->start_time, $tz)->format('H:i:s'); // e.g. "21:00:00"
        $endTime   = Carbon::parse($this->my_shift->end_time, $tz)->format('H:i:s');   // e.g. "01:36:35"
    
        // Build default shift times on today's date.
        $shiftStart = $today->copy()->setTimeFromTimeString($startTime);
        $shiftEnd   = $today->copy()->setTimeFromTimeString($endTime);
    
        // Handle cross-midnight shifts.
        if ($startTime > $endTime) {
            if ($now->format('H:i:s') < $endTime) {
                $shiftStart = $today->copy()->subDay()->setTimeFromTimeString($startTime);
                $shiftEnd   = $today->copy()->setTimeFromTimeString($endTime);
            } else {
                $shiftStart = $today->copy()->setTimeFromTimeString($startTime);
                $shiftEnd   = $today->copy()->addDay()->setTimeFromTimeString($endTime);
            }
        }
    
        // Manually subtract 3 hours (10800 seconds) from the computed timestamps.
        $shiftStart = Carbon::createFromTimestamp($shiftStart->timestamp - 10800, $tz);
        $shiftEnd   = Carbon::createFromTimestamp($shiftEnd->timestamp - 10800, $tz);
    
        // Calculate total duration in seconds using timestamps.
        $shiftDuration = $shiftEnd->timestamp - $shiftStart->timestamp;
    
        // Calculate elapsed time and progress.
        if ($now->lt($shiftStart)) {
            $elapsed = 0;
            $progress = 0;
        } elseif ($now->gte($shiftEnd)) {
            $elapsed = $shiftDuration;
            $progress = 100;
        } else {
            $elapsed = $now->timestamp - $shiftStart->timestamp;
            $progress = ($elapsed / $shiftDuration) * 100;
        }
    
        return [
            'now'           => $now,
            'shiftStart'    => $shiftStart,
            'shiftEnd'      => $shiftEnd,
            'shiftDuration' => $shiftDuration,
            'elapsed'       => $elapsed,
            'progress'      => $progress,
        ];
    }

    public function render()
    {
        return view('livewire.shift-progress');
    }
}