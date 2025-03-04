<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnfairFeedback extends Model
{
    use HasFactory;

    protected $fillable = ['feedback_id', 'user_id', 'justification'];

    // Unfair Feedback belongs to Feedback
    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    // Unfair Feedback belongs to the User who marked it unfair
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}