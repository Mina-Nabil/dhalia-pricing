<?php

namespace App\Providers;

use App\Exceptions\CurrencyManagementException;
use App\Models\Currency;
use App\Policies\CurrencyPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{

    public function getCurrencies()
    {
        Gate::authorize('view-currency-list');
        return Currency::all();
    }

    public function getCurrency($id)
    {
        return Currency::findOrFail($id);
    }

    public function createCurrency($name, $rate, $code = null)
    {
        Gate::authorize('create-currency');
        try {
            return Currency::create([
                'name' => $name,
                'code' => $code,
                'rate' => $rate,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new CurrencyManagementException('Failed to create currency');
        }
    }

    public function updateCurrency(Currency $currency, $name, $code, $rate)
    {
        Gate::authorize('update-currency', $currency);
        try {
            $currency->update([
                'name' => $name,
                'code' => $code,
                'rate' => $rate,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new CurrencyManagementException('Failed to update currency');
        }
    }


    public function deleteCurrency(Currency $currency)
    {
        try {
            $currency->delete();
        } catch (Exception $e) {
            report($e);
            throw new CurrencyManagementException('Failed to delete currency');
        }
    }




    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyServiceProvider::class, function ($app) {
            return new CurrencyServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-currency', [CurrencyPolicy::class, 'view']);
        Gate::define('view-currency-list', [CurrencyPolicy::class, 'viewAny']);
        Gate::define('create-currency', [CurrencyPolicy::class, 'create']);
        Gate::define('update-currency', [CurrencyPolicy::class, 'update']);
        Gate::define('delete-currency', [CurrencyPolicy::class, 'delete']);
    }
}
