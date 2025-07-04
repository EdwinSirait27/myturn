<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
//     public function handle($request, Closure $next, ...$guards)
// {
//     if (Auth::check()) {
//         $user = auth()->user();
        
//         if ($user->can('isAdmin')) {
//             return redirect('/dashboardAdmin');
//         }
//         if ($user->can('isHR')) {
//             return redirect('/dashboardHR');
//         }
//         if ($user->can('isHeadHR')) {
//             return redirect('/dashboardHR');
//         }
//         if ($user->can('isHR')) {
//             return redirect('/dashboardHR');
//         }
//         if ($user->can('isManagerStore')) {
//             return redirect('/dashboardManager');
//         }
//         if ($user->can('isSupervisor')) {
//             return redirect('/dashboardSupervisor');
//         }
//         if ($user->can('isHeadBuyer')) {
//             return redirect('/dashboardHeadBuyer');
//         }
//         if ($user->can('isBuyer')) {
//             return redirect('/dashboardBuyer');
//         }

//         // Jika user_type tidak valid, logout dan kembali ke login
//         Auth::logout();
//         return redirect('/')->withErrors(['error' => 'Access Denied, please contact Edw for more information.']);
//     }

//     return $next($request);
// }
public function handle($request, Closure $next, ...$guards)
{
    if (Auth::check()) {
        $user = auth()->user();
        $activeRole = session('active_role') ?? $user->active_role ?? $user->getRoleNames()->first();

        switch ($activeRole) {
            case 'Admin':
                return redirect('/dashboardAdmin');
            case 'HR':
            case 'HeadHR':
                return redirect('/dashboardHR');
            case 'ManagerStore':
                return redirect('/dashboardManager');
            case 'supervisor-store':
                return redirect('/dashboardSupervisor');
            case 'HeadBuyer':
                return redirect('/dashboardHeadBuyer');
            case 'Buyer':
                return redirect('/dashboardBuyer');
            default:
                Auth::logout();
                return redirect('/')->withErrors([
                    'error' => 'Access Denied. Role not recognized. Please contact Edw.'
                ]);
        }
    }

    return $next($request);
}
}