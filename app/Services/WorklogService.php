<?php

namespace App\Services;

use App\Models\WorkLog;
use App\Models\WorkDay;

class WorklogService
{
    /**
     * Start a new work log.
     *
     * @param  int    $employeeId
     * @param  int    $shiftId
     * @param  int    $projectId
     * @param  int    $projectStatusId
     * @param  bool   $onOutlier
     * @return WorkLog
     */
    public function startWorklog($employeeId, $shiftId, $projectId, $projectStatusId, $onOutlier = false)
    {
        // Get or create the current work day record for this employee on the current date.
        $workDay = WorkDay::firstOrCreate(
            ['employee_id' => $employeeId, 'date' => now()->format('Y-m-d')],
            ['shift_id' => $shiftId, 'location' => 'office'] // Adjust location logic if needed.
        );
    
        // Create and return a new work log record.
        return WorkLog::create([
            'work_day_id'        => $workDay->id,
            'project_id'         => $projectId,
            'project_status_id'  => $projectStatusId,
            'on_outlier'         => $onOutlier,
            'start_time'         => now(),
        ]);
    }

    /**
     * End an active work log.
     *
     * @param  int    $workLogId
     * @param  int    $taskCount
     * @param  string $taskDescription
     * @return WorkLog
     */
    public function endWorklog($workLogId, $taskCount, $taskDescription = null)
    {
        $workLog = WorkLog::findOrFail($workLogId);
        $workLog->update([
            'task_count'      => $taskCount,
            'task_description'=> $taskDescription,
            'end_time'        => now(),
        ]);

        return $workLog;
    }

    /**
     * Get the work log timeline for an employee on a given day.
     *
     * @param  int    $employeeId
     * @param  string $date
     * @return \Illuminate\Support\Collection
     */
    public function getWorklogTimeline($employeeId, $date)
    {
        // Assuming that WorkLog has a relation "workDay" and that WorkDay holds the date and employee_id.
        $timeline = WorkLog::whereHas('workDay', function ($query) use ($employeeId, $date) {
            $query->where('employee_id', $employeeId)
                  ->where('date', $date);
        })->with('project', 'projectStatus', 'workDay')->get();

        return $timeline;
    }
}