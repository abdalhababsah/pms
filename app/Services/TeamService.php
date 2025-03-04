<?php

namespace App\Services;

use App\Models\User;
use App\Models\TeamLeaderAssignment;
use Illuminate\Http\Request;

class TeamService
{
    /**
     * Retrieve the team leader and assigned team members.
     *
     * @param int $id
     * @return array
     */
    public function viewTeam($id)
    {
        $teamLeader = User::findOrFail($id);
        $teamMembers = $teamLeader->teamLeaderAssignments()->with('employee')->get();
        return compact('teamLeader', 'teamMembers');
    }

    /**
     * Return the rendered partial view of current team members.
     *
     * @param int $teamLeaderId
     * @return string
     */
    public function getTeamMembers($teamLeaderId)
    {
        $teamLeader = User::findOrFail($teamLeaderId);
        $teamMembers = $teamLeader->teamLeaderAssignments()->with('employee')->get();
        return view('team.partials.team_members', compact('teamMembers'))->render();
    }

    /**
     * Assign a team member to a team leader.
     * If the employee is already assigned to any team leader, remove that assignment first.
     *
     * @param Request $request
     * @param int $id  Team Leader ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignTeam(Request $request, $id)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        $teamLeader = User::findOrFail($id);
        if ($teamLeader->role->id != 2) {
            return response()->json(['error' => 'The selected user is not a team leader.'], 422);
        }

        $employeeId = $validatedData['employee_id'];

        // Remove any existing assignment for this employee.
        TeamLeaderAssignment::where('employee_id', $employeeId)->delete();

        // Create the new assignment.
        TeamLeaderAssignment::create([
            'team_leader_id' => $id,
            'employee_id'    => $employeeId,
        ]);

        return response()->json(['success' => 'Team member assigned successfully.']);
    }

    /**
     * Search for employees (role 3) excluding those already assigned to the given team leader.
     *
     * @param Request $request
     * @return string
     */
    public function searchEmployees(Request $request)
    {
        $query = $request->input('q');
        $teamLeaderId = $request->input('team_leader_id');

        $assignedEmployeeIds = [];
        if ($teamLeaderId) {
            $assignedEmployeeIds = TeamLeaderAssignment::where('team_leader_id', $teamLeaderId)
                ->pluck('employee_id')
                ->toArray();
        }

        $employees = User::where('role_id', 3)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%$query%")
                  ->orWhere('last_name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%");
            })
            ->when(!empty($assignedEmployeeIds), function ($q) use ($assignedEmployeeIds) {
                $q->whereNotIn('id', $assignedEmployeeIds);
            })
            ->limit(10)
            ->get();

        return view('team.partials.employee_cards', compact('employees'))->render();
    }

    /**
     * Remove a team member assignment.
     *
     * @param Request $request
     * @param int $assignmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeTeamMember(Request $request, $assignmentId)
    {
        $assignment = TeamLeaderAssignment::findOrFail($assignmentId);
        $assignment->delete();
        return response()->json(['success' => 'Team member removed successfully.']);
    }

    /**
     * Dummy performance method.
     *
     * @param int $id
     * @return string
     */
    public function performance($id)
    {
        return "Employee performance for ID: $id";
    }
}