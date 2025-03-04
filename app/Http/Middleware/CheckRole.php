<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles  Comma-separated list of role IDs
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access.');
        }
        
        $allowedRoleIds = array_map('trim', explode(',', $roles));
        if (!in_array(Auth::user()->role->id, $allowedRoleIds)) {
            abort(403, 'Unauthorized access.');
        }
        
        return $next($request);
    }
}