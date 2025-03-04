<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    protected AdminUserService $adminUserService;

    public function __construct(AdminUserService $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }

    /**
     * Display a paginated list of employees.
     */
    public function employees(Request $request)
    {
        $employees = $this->adminUserService->getEmployees($request);
        return view('admin.users.employees.index', compact('employees'));
    }

    /**
     * Display a list of team leaders.
     */
    public function teamLeaders()
    {
        $teamLeaders = $this->adminUserService->getTeamLeaders();
        return view('admin.users.teamleaders.index', compact('teamLeaders'));
    }

    /**
     * Store a new user (employee or team leader).
     */
    public function store(Request $request)
    {
        $this->adminUserService->createUser($request);
        return redirect()->back()->with('success', 'User created successfully.');
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, $id)
    {
        $this->adminUserService->updateUser($request, $id);
        return redirect()->back()->with('success', 'User updated successfully.');
    }
}