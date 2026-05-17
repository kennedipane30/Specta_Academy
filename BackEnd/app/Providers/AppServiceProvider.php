<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Enrollment;
use App\Models\DedicatedTutor;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.spekta', function ($view) {
            $pendingEnrollment = 0;
            $pendingTutor = 0;

            if (auth()->check() && auth()->user()->role_id == 1) {
                $pendingEnrollment = Enrollment::where('status', 'pending')->count();
                $pendingTutor = DedicatedTutor::where('status', 'pending')->count();
            }

            $view->with([
                'layoutPendingEnrollment' => $pendingEnrollment,
                'layoutPendingTutor' => $pendingTutor,
                'layoutNotificationCount' => $pendingEnrollment + $pendingTutor,
            ]);
        });
    }
}