<?php

namespace App\Providers;

use Laravel\Sanctum\Sanctum;
use App\Models\EchantillonEnquete;
use App\Models\PersonalAccessToken;
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
                Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

    }
}
