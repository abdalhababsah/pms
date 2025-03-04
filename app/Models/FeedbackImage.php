<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackImage extends Model
{
    use HasFactory;

    protected $fillable = ['feedback_id', 'image_url'];

    // A Feedback Image belongs to Feedback
    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}