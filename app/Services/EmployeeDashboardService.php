<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Feedback;

class EmployeeDashboardService
{
    /**
     * Calculate working hours from work logs for the given user.
     *
     * @param int $userId
     * @return array  [
     *    'total_hours' => float, 
     *    'outlier_hours' => float, 
     *    'non_outlier_hours' => float
     * ]
     */
    public function calculateWorkHours($userId, $workDayDate)
    {
        $totalSeconds = DB::table('work_logs')
            ->join('work_days', 'work_logs.work_day_id', '=', 'work_days.id')
            ->where('work_days.employee_id', $userId)
            ->where('work_days.date', $workDayDate)
            ->whereNotNull('work_logs.end_time')
            ->sum(DB::raw('TIMESTAMPDIFF(SECOND, start_time, end_time)'));

        $outlierSeconds = DB::table('work_logs')
            ->join('work_days', 'work_logs.work_day_id', '=', 'work_days.id')
            ->where('work_days.employee_id', $userId)
            ->where('work_days.date', $workDayDate)
            ->where('on_outlier', 1)
            ->whereNotNull('work_logs.end_time')
            ->sum(DB::raw('TIMESTAMPDIFF(SECOND, start_time, end_time)'));

        $nonOutlierSeconds = DB::table('work_logs')
            ->join('work_days', 'work_logs.work_day_id', '=', 'work_days.id')
            ->where('work_days.employee_id', $userId)
            ->where('work_days.date', $workDayDate)
            ->where('on_outlier', 0)
            ->whereNotNull('work_logs.end_time')
            ->sum(DB::raw('TIMESTAMPDIFF(SECOND, start_time, end_time)'));

        return [
            'total_hours'       => $totalSeconds / 3600,
            'outlier_hours'     => $outlierSeconds / 3600,
            'non_outlier_hours' => $nonOutlierSeconds / 3600,
        ];
    }


    /**
     * Get feedback statistics for the given user.
     *
     * @param int $userId
     * @return array
     */
    public function getFeedbackStats($userId)
    {
        $averageRating = Feedback::where('employee_id', $userId)
            ->whereDoesntHave('unfairFeedback')
            ->avg('rating') ?? 0;

        $fairCount = Feedback::where('employee_id', $userId)
            ->whereDoesntHave('unfairFeedback')
            ->count();

        $unfairCount = Feedback::where('employee_id', $userId)
            ->whereHas('unfairFeedback')
            ->count();

        return [
            'average_rating'        => $averageRating,
            'fair_feedback_count'   => $fairCount,
            'unfair_feedback_count' => $unfairCount,
        ];
    }
}