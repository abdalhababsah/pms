<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    /**
     * Retrieve performance data for the employee with the given ID.
     *
     * @param int $employeeId
     * @return array
     */
    public function getPerformanceData(int $employeeId): array
    {
        // Get the employee record.
        $employee = User::findOrFail($employeeId);

        // Overall average feedback (excluding feedback flagged as unfair).
        $overallAvg = Feedback::where('employee_id', $employeeId)
            ->whereDoesntHave('unfairFeedback')
            ->avg('rating');

        // Total feedback count (excluding unfair feedback).
        $feedbackCount = Feedback::where('employee_id', $employeeId)
            ->whereDoesntHave('unfairFeedback')
            ->count();

        // Group feedback by project for this employee (excluding unfair feedback).
        $projectFeedback = Feedback::where('employee_id', $employeeId)
            ->whereDoesntHave('unfairFeedback')
            ->selectRaw('project_id, AVG(rating) as avg_rating, COUNT(*) as count')
            ->groupBy('project_id')
            ->get();
        // Eager-load the related project.
        $projectFeedback->load('project');

        // Determine feedback timeline data: from the first feedback date to today.
        $firstFeedbackDate = Feedback::where('employee_id', $employeeId)
            ->whereDoesntHave('unfairFeedback')
            ->min(DB::raw('DATE(created_at)'));
        $startDate = $firstFeedbackDate ? Carbon::parse($firstFeedbackDate) : Carbon::now();
        $endDate = Carbon::now();
        $timelineData = Feedback::where('employee_id', $employeeId)
            ->whereDoesntHave('unfairFeedback')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('DATE(created_at) as date, AVG(rating) as avg_rating')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();

        // Retrieve the WorkDay record for a specific date.
        // Use the 'filter_date' query parameter; default to yesterday.
        $filterDate = request()->get('filter_date', Carbon::yesterday()->toDateString());
        $workDay = WorkDay::where('employee_id', $employeeId)
            ->whereDate('date', $filterDate)
            ->first();
        // Get work logs for that WorkDay if it exists.
        $workLogs = $workDay ? $workDay->workLogs()->with('project')->get() : collect();

        // Rating distribution: count feedbacks per rating (from 1 to 5) excluding unfair feedback.
        $ratingDistribution = Feedback::where('employee_id', $employeeId)
            ->whereDoesntHave('unfairFeedback')
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->all();

        return [
            'employee'            => $employee,
            'overallAvg'          => $overallAvg,
            'feedbackCount'       => $feedbackCount,
            'projectFeedback'     => $projectFeedback,
            'timelineData'        => $timelineData,
            'ratingDistribution'  => $ratingDistribution,
            'workDay'             => $workDay,
            'workLogs'            => $workLogs,
        ];
    }
}