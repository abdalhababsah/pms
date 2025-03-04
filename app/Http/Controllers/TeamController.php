<?php

namespace App\Http\Controllers;

use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function viewTeam($id)
    {
        $data = $this->teamService->viewTeam($id);
        return view('team.index', $data);
    }
    
    public function getTeamMembers(Request $request, $teamLeaderId)
    {
        return $this->teamService->getTeamMembers($teamLeaderId);
    }

    public function assignTeam(Request $request, $id)
    {
        return $this->teamService->assignTeam($request, $id);
    }

    public function searchEmployees(Request $request)
    {
        return $this->teamService->searchEmployees($request);
    }

    public function removeTeamMember(Request $request, $assignmentId)
    {
        return $this->teamService->removeTeamMember($request, $assignmentId);
    }

    public function performance($id)
    {
        return $this->teamService->performance($id);
    }
}