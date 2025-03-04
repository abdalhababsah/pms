<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamLeaderDashboardController extends Controller
{
    public function index(){
        return view('teamlead.dashboard');
    }
}
