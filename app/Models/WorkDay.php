<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDay extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'shift_id', 'location'];

    // A Work Day belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // A Work Day belongs to a Shift
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // A Work Day has many Work Logs
    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }
}