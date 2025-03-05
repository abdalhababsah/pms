<?php
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Employee\WorklogController;
use App\Http\Controllers\TeamLead\TeamLeaderDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/employee/{id}/worklog-timeline', [PerformanceController::class, 'getWorklogTimeline'])
    ->name('employee.worklog.timeline');

});


Route::middleware(['auth', 'role:1,2'])->group(function () {
    Route::get('/team-leaders/{id}/view-team', [TeamController::class, 'viewTeam'])
        ->name('admin.teamLeaders.viewTeam');
    Route::get('/team-leaders/{id}/members', [TeamController::class, 'getTeamMembers'])
        ->name('team.members.get');
    Route::post('/team-leaders/{id}/assign-team', [TeamController::class, 'assignTeam'])
        ->name('admin.teamLeaders.assignTeam');
    Route::delete('/team-leaders/remove/{id}', [TeamController::class, 'removeTeamMember'])
        ->name('team.teamMember.remove');
    Route::get('/team/employees/search', [TeamController::class, 'searchEmployees'])
        ->name('team.employees.search');
    Route::get('/employee/{id}/performance', [TeamController::class, 'performance'])
        ->name('employee.performance');
});
// Admin Routes: Only accessible by users with `admin` role
Route::middleware(['auth', 'role:1'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/employee/{id}/performance', [PerformanceController::class, 'performance'])
         ->name('employee.performance.admin');
    // Employee Management Routes (Listing remains under /employees)
    Route::get('/employees', [AdminUsersController::class, 'employees'])->name('admin.employees'); // List employees
    // team-leaders Management Routes (Listing remains under /team-leaders)
    Route::get('/team-leaders', [AdminUsersController::class, 'teamLeaders'])->name('admin.teamLeaders');


    // Route for assigning team members
    Route::post('/team-leaders/{id}/assign-team', [TeamController::class, 'assignTeam'])
        ->name('admin.teamLeaders.assignTeam');

    // User Management Routes (Store, Edit, Update, Destroy - work for both employees and team leaders)
    Route::post('/users', [AdminUsersController::class, 'store'])->name('admin.users.store'); // Create employee/team leader
    Route::get('/users/{id}/edit', [AdminUsersController::class, 'edit'])->name('admin.users.edit'); // Show edit form
    Route::put('/users/{id}', [AdminUsersController::class, 'update'])->name('admin.users.update'); // Update user
    Route::delete('/users/{id}', [AdminUsersController::class, 'destroy'])->name('admin.users.destroy'); // Delete user
});

// Team Lead Routes: Only accessible by users with `teamlead` role
Route::middleware(['auth', 'role:2'])->prefix('teamlead')->group(function () {
    Route::get('/dashboard', [TeamLeaderDashboardController::class, 'index'])->name('teamlead.dashboard');
});
// The custom middleware "check.team.member" should check that the employee (passed via {id}) is assigned to the logged-in team leader.
Route::middleware(['auth', 'role:2', 'check.team.member'])->group(function () {
    Route::get('/employee/{id}/performance', [PerformanceController::class, 'performance'])
         ->name('employee.performance.teamleader');
});
// Employee Routes: Only accessible by users with `employee` role
Route::middleware(['auth', 'role:3'])->prefix('employee')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    Route::post('worklog/start', [WorklogController::class, 'start'])->name('employee.worklog.start');
    Route::post('worklog/end', [WorklogController::class, 'end'])->name('employee.worklog.end');
    Route::get('worklog/timeline', [WorklogController::class, 'timeline'])->name('employee.worklog.timeline');
    Route::get('/my-performance', [PerformanceController::class, 'performanceForAuth'])
    ->name('employee.performance.self');
});

// General Dashboard Route
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Error Routes
Route::get('/error/404', fn() => response()->view('errors.error404', [], 404))->name('error.404');
Route::get('/error/500', fn() => response()->view('errors.error500', [], 500))->name('error.500');
Route::get('/error/403', fn() => response()->view('errors.error403', [], 403))->name('error.403');

// Fallback Route for Undefined URLs
Route::fallback(fn() => redirect()->route('error.404'));

// Authentication Routes
require __DIR__ . '/auth.php';