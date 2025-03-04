<?php

namespace App\Http\Controllers;

use App\Models\WorkDay;
use App\Services\PerformanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    protected PerformanceService $performanceService;

    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Display performance statistics for an employee by given ID.
     * Used by admins and team leaders.
     */
    public function performance($id)
    {
        $data = $this->performanceService->getPerformanceData($id);
        return view('employee.performance', $data);
    }

    /**
     * Display performance statistics for the authenticated employee.
     */
    public function performanceForAuth(Request $request)
    {
        $employeeId = $request->user()->id;
        $data = $this->performanceService->getPerformanceData($employeeId);
        return view('employee.performance', $data);
    }


    public function getWorklogTimeline(Request $request, $id)
    {
        // Get the filter date from the query parameter; default to yesterday.
        $filterDate = $request->query('filter_date', Carbon::yesterday()->toDateString());

        // Retrieve the WorkDay record for this employee on the given date.
        $workDay = WorkDay::where('employee_id', $id)
            ->whereDate('date', $filterDate)
            ->first();

        // Load work logs (with related project info) if the work day exists.
        $workLogs = $workDay ? $workDay->workLogs()->with('project')->get() : collect();

        // Return JSON response.
        return response()->json([
            'workDay'  => $workDay,
            'workLogs' => $workLogs,
        ]);
    }
}