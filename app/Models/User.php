<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // A User belongs to a Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // A User can be a Team Leader for many employees
    public function teamLeaderAssignments()
    {
        return $this->hasMany(TeamLeaderAssignment::class, 'team_leader_id');
    }

    // A User can be an employee under multiple team leaders
    public function employeeAssignments()
    {
        return $this->hasMany(TeamLeaderAssignment::class, 'employee_id');
    }

    // A User has many Work Days
    public function workDays()
    {
        return $this->hasMany(WorkDay::class, 'employee_id');
    }

    // A User has many Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // A User submits many Feedbacks
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'employee_id');
    }

    // A User can mark feedback as unfair
    public function unfairFeedbacks()
    {
        return $this->hasMany(UnfairFeedback::class, 'user_id');
    }

    public function getMonthlyAverageFeedbackAttribute()
    {
        $query = $this->feedbacks()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->whereDoesntHave('unfairFeedback');
        return $query->avg('rating') ?? 0;
    }

    public function getMonthlyFeedbackCountAttribute()
    {
        $query = $this->feedbacks()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->whereDoesntHave('unfairFeedback');
        return $query->count();
    }
}
