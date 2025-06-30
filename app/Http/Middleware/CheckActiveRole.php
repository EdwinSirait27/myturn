<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
 public function handle(Request $request, Closure $next, ...$roles)
{
    $activeRole = session('active_role');

    if (!$activeRole || !in_array($activeRole, $roles)) {
        abort(403, 'Access denied based on active role.');
    }

    return $next($request);
}

}
