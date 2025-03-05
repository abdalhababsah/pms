<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WorklogService;
use Illuminate\Support\Facades\Auth;

class WorklogController extends Controller
{
    protected $worklogService;

    public function __construct(WorklogService $worklogService)
    {
        $this->worklogService = $worklogService;
    }

    /**
     * Start a new work log.
     */
    public function start(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'shift_id'          => 'required|exists:shifts,id',
            'project_id'        => 'required|exists:projects,id',
            'project_status_id' => 'required|exists:statuses,id',
            'on_outlier'        => 'nullable|boolean',
        ]);

        $employeeId = Auth::id();

        $this->worklogService->startWorklog(
            $employeeId,
            $validated['shift_id'],
            $validated['project_id'],
            $validated['project_status_id'],
            $validated['on_outlier'] ?? false
        );

        return redirect()->back()->with('success', 'Work log started successfully!');
    }

    /**
     * End an active work log.
     */
    public function end(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'work_log_id'     => 'required|exists:work_logs,id',
            'task_count'      => 'required|integer',
            'task_description'=> 'nullable|string',
        ]);

        $this->worklogService->endWorklog(
            $validated['work_log_id'],
            $validated['task_count'],
            $validated['task_description'] ?? null
        );

        return redirect()->back()->with('success', 'Work log ended successfully!');
    }

    /**
     * Get the work log timeline.
     *
     * This method is no longer used for AJAX but is kept for completeness.
     */
    public function timeline(Request $request)
    {
        $employeeId = Auth::id();
        $date = $request->input('date', now()->format('Y-m-d'));
        $timeline = $this->worklogService->getWorklogTimeline($employeeId, $date);

        return view('employee.partials.timeline', compact('timeline'));
    }
}