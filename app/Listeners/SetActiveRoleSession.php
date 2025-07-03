<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;

class SetActiveRoleSession
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
   public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user->active_role && $user->hasRole($user->active_role)) {
            session(['active_role' => $user->active_role]);
            \Log::info('SESSION SET via listener: ' . $user->active_role);
        } else {
            $first = $user->getRoleNames()->first();
            session(['active_role' => $first]);
            \Log::info('SESSION SET via listener fallback: ' . $first);
        }
    }
}