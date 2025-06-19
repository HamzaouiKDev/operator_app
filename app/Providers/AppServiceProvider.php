<?php

namespace App\Providers;

use App\Models\EchantillonEnquete;
use Illuminate\Support\ServiceProvider;
use App\Observers\EchantillonEnqueteObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
                EchantillonEnquete::observe(EchantillonEnqueteObserver::class);

    }
}
