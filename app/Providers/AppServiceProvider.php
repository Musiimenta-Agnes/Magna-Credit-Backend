<?php

namespace App\Providers;

use App\Models\LoanApplication;
use App\Models\Repayment;
use App\Observers\LoanApplicationObserver;
use App\Observers\RepaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Notifications\ResetPassword::class,
            \App\Notifications\ResetPasswordNotification::class
        );
    }

    public function boot(): void
    {
        // Boot observers
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        LoanApplication::observe(LoanApplicationObserver::class);
        Repayment::observe(RepaymentObserver::class);

        // Listen for Login and Logout Events
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            function ($event) {
                \App\Models\ActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'Logged In',
                    'description' => "User '{$event->user->name}' ({$event->user->email}) logged into the dashboard.",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function ($event) {
                if ($event->user) {
                    \App\Models\ActivityLog::create([
                        'user_id' => $event->user->id,
                        'action' => 'Logged Out',
                        'description' => "User '{$event->user->name}' ({$event->user->email}) logged out of the dashboard.",
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
        );
        
        \Opcodes\LogViewer\Facades\LogViewer::auth(function ($request) {
            // Allow access without login for initial deployment debugging.
            // Once user auth is working, change to: return $request->user() !== null;
            return true;
        });
    }
}
