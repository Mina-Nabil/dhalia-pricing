<?php

namespace App\Providers;

use App\Policies\OfferPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class OfferServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OfferServiceProvider::class, function ($app) {
            return new OfferServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-offers-list', [OfferPolicy::class, 'viewAny']);
        Gate::define('view-offer', [OfferPolicy::class, 'view']);
        Gate::define('create-offers', [OfferPolicy::class, 'create']);
        Gate::define('update-offers', [OfferPolicy::class, 'update']);
        Gate::define('delete-offers', [OfferPolicy::class, 'delete']);
    }
}
