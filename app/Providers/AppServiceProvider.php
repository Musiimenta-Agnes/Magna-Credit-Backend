<?php

namespace App\Providers;

use App\Models\LoanApplication;
use App\Models\Repayment;
use App\Observers\LoanApplicationObserver;
use App\Observers\RepaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        LoanApplication::observe(LoanApplicationObserver::class);
        Repayment::observe(RepaymentObserver::class);
    }
}
