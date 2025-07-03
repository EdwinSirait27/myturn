<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Spatie\Activitylog\Models\Activity;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Http\Responses\CustomLoginResponse;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    //     Activity::created(function ($log) {
    //     if ($log->log_name !== 'vendorgroup' || !$log->causer_id) {
    //         return;
    //     }

    //     $userId = $log->causer_id;

    //     $count = Activity::where('log_name', 'vendorgroup')
    //         ->where('causer_id', $userId)
    //         ->count();

    //     if ($count > 5) {
    //         $toDelete = $count - 5;

    //         Activity::where('log_name', 'vendorgroup')
    //             ->where('causer_id', $userId)
    //             ->orderBy('created_at', 'asc')
    //             ->limit($toDelete)
    //             ->delete();
    //     }
    // });
    Activity::created(function ($log) {
    $logNamesWithLimits = [
        'vendorgroup' => 50,
        'vendor' => 50,
        'customer' => 50,
    ];

    $logName = $log->log_name;
    $userId = $log->causer_id;

    if (!$userId || !isset($logNamesWithLimits[$logName])) {
        return;
    }

    $limit = $logNamesWithLimits[$logName];

    $count = Activity::where('log_name', $logName)
        ->where('causer_id', $userId)
        ->count();

    if ($count > $limit) {
        $toDelete = $count - $limit;

        Activity::where('log_name', $logName)
            ->where('causer_id', $userId)
            ->orderBy('created_at', 'asc')
            ->limit($toDelete)
            ->delete();
    }
});

    }
}
