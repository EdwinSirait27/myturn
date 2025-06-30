<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class PermissionFromActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();
        $activeRole = session('active_role');

        if (!$user || !$activeRole) {
            abort(403, 'Active role not found.');
        }

        $role = Role::where('name', $activeRole)->with('permissions')->first();

        if (!$role || !$role->permissions->pluck('name')->contains($permission)) {
            abort(403, 'Permission denied for active role.');
        }

        return $next($request);
    }
}
