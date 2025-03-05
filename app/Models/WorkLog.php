<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_day_id',
        'project_id',
        'project_status_id',
        'on_outlier',
        'task_count',
        'start_time',
        'end_time',
        'task_description',
    ];
    // A Work Log belongs to a Work Day
    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    // A Work Log belongs to a Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // A Work Log belongs to a Project Status
    public function projectStatus()
    {
        return $this->belongsTo(Status::class, 'project_status_id');
    }
}