<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'project_id', 'rating', 'justification'];

    // A Feedback belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // A Feedback belongs to a Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // A Feedback has many Images
    public function images()
    {
        return $this->hasMany(FeedbackImage::class, 'feedback_id');
    }

    // A Feedback can have one UnfairFeedback record
    public function unfairFeedback()
    {
        return $this->hasOne(UnfairFeedback::class, 'feedback_id');
    }
}