<?php

namespace App\Providers;

use App\Exceptions\CurrencyManagementException;
use App\Models\AppLog;
use App\Models\Currency;
use App\Policies\CurrencyPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{

    public function getCurrencies($search = null, $paginate = 10, $forDropdown = false)
    {
        if (!$forDropdown) {
            Gate::authorize('view-currency-list');
            AppLog::info('Currencies list viewed', 'Currencies loaded');
        }
        $query = Currency::when($search !== null, function ($q) use ($search) {
            $q->bySearch($search);
        })->orderBy('name');
        return $paginate && !$forDropdown ? $query->paginate($paginate) : $query->get();
    }

    public function getCurrency($id)
    {
        $currency = Currency::findOrFail($id);
        Gate::authorize('view-currency', $currency);
        AppLog::info('Currency viewed', 'Currency ' . $id . ' viewed', $currency);
        return $currency;
    }

    public function createCurrency($name, $rate, $code = null)
    {
        Gate::authorize('create-currency');
        AppLog::info('Currency created', 'Currency ' . $name . ' created', $name);
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
        AppLog::info('Currency updated', 'Currency ' . $currency->name . ' updated', $currency);
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
        Gate::authorize('delete-currency', $currency);
        AppLog::info('Currency deleted', 'Currency ' . $currency->name . ' deleted', $currency);
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
