<?php

namespace App\Http\Responses;


use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Log;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // ✅ Ambil dari session, fallback ke user->active_role
        $role = session('active_role') ?? $request->user()?->active_role;

        Log::info('Final Redirect Role: ' . ($role ?? 'null'));
          \Log::info('✅ CustomLoginResponse fired. Role: ' . $role);

        $redirect = match ($role) {
            'Admin' => '/dashboardAdmin',
            'HeadHR', 'HR' => '/dashboardHR',
            'Buyer' => '/dashboardBuyer',
            default => '/',
        };

        return redirect()->intended($redirect);
    }
}