<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status', 'start_date', 'end_date'];

    // A Project has many Work Logs
    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }

    // A Project has many Feedbacks
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}