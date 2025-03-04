<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserService
{
    /**
     * Retrieve and paginate employees (role 3) based on filters.
     */
    public function getEmployees(Request $request)
    {
        $searchId = $request->input('id');
        $searchName = $request->input('name');
        $searchEmail = $request->input('email');

        $query = User::whereHas('role', function ($query) {
            $query->where('id', 3);
        });

        if (!empty($searchId)) {
            $query->where('id', $searchId);
        }
        if (!empty($searchName)) {
            $query->where(function ($q) use ($searchName) {
                $q->where('first_name', 'like', "%$searchName%")
                  ->orWhere('last_name', 'like', "%$searchName%");
            });
        }
        if (!empty($searchEmail)) {
            $query->where('email', 'like', "%$searchEmail%");
        }

        return $query->paginate(10);
    }

    /**
     * Retrieve all team leaders (role 2).
     */
    public function getTeamLeaders()
    {
        return User::whereHas('role', function ($query) {
            $query->where('id', 2);
        })->get();
    }

    /**
     * Create a new user (employee or team leader).
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:8',
            'role_id'    => 'required', // Should be 2 for team leader or 3 for employee
        ]);

        return User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role_id'    => $request->role_id,
        ]);
    }

    /**
     * Update an existing user.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id'    => 'required', // 2 or 3
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'role_id'    => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return $user;
    }
}