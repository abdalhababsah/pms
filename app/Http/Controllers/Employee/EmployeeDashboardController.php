<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Status;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\EmployeeDashboardService;
use App\Services\ShiftTimeCalculator;

class EmployeeDashboardController extends Controller
{
    protected $worklogService;
    protected $dashboardService;
    protected $shiftTimeCalculator;

    public function __construct(
        \App\Services\WorklogService $worklogService, 
        EmployeeDashboardService $dashboardService,
        ShiftTimeCalculator $shiftTimeCalculator
    ) {
        $this->worklogService = $worklogService;
        $this->dashboardService = $dashboardService;
        $this->shiftTimeCalculator = $shiftTimeCalculator;
    }

    public function index()
    {
        // Get the logged-in user's shift.
        $my_shift = auth()->user()->shift;

        // Find any active work log for today.
        $activeWorkLog = WorkLog::whereDate('start_time', Carbon::today())
            ->whereNull('end_time')
            ->first();

        // Use our helper to compute the adjusted shift times.
        $shiftData = $this->shiftTimeCalculator->calculate($my_shift);
        $isWithinShift = $shiftData['isWithinShift'];

        /*
         * We want to use the date corresponding to the shift start time.
         * If the shift started yesterday (for cross‑midnight shifts), then we use yesterday.
         * Otherwise, we use today's date.
         */
        $workDayDate = $shiftData['shiftStart']->format('Y-m-d');

        // Retrieve timeline data using the work day date.
        $timeline = $this->worklogService->getWorklogTimeline(auth()->id(), $workDayDate);

        // Get projects and statuses.
        $projects = Project::all();
        $statuses = Status::all();

        // Get working hours and feedback statistics using the dashboard service,
        // passing the work day date (which is either today or yesterday at maximum).
        $workHoursData = $this->dashboardService->calculateWorkHours(auth()->id(), $workDayDate);
        $feedbackStats = $this->dashboardService->getFeedbackStats(auth()->id());

        return view('employee.dashboard', compact(
            'my_shift',
            'activeWorkLog',
            'isWithinShift',
            'timeline',
            'projects',
            'statuses',
            'workHoursData',
            'feedbackStats'
        ));
    }
}