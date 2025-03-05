<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['name'];
    public function worklogs()
    {
        return $this->hasMany(Worklog::class);
    }
}
