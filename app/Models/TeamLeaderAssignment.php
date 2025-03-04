<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamLeaderAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['team_leader_id', 'employee_id'];

    // A TeamLeaderAssignment belongs to a Team Leader
    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    // A TeamLeaderAssignment belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}