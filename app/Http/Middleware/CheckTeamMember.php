<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TeamLeaderAssignment;

class CheckTeamMember
{
    public function handle(Request $request, Closure $next)
    {
        $teamLeader = Auth::user();
        $employeeId = $request->route('id');

        // Check if the employee is assigned to this team leader.
        $isAssigned = TeamLeaderAssignment::where('team_leader_id', $teamLeader->id)
                    ->where('employee_id', $employeeId)
                    ->exists();

        if (!$isAssigned) {
            abort(403, 'You are not authorized to view this employee’s performance.');
        }

        return $next($request);
    }
}