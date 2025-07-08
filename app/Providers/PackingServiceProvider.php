<?php

namespace App\Providers;

use App\Exceptions\PackingManagementException;
use App\Models\AppLog;
use App\Models\Packing;
use App\Policies\PackingPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PackingServiceProvider extends ServiceProvider
{

    public function getPackings($search = null, $paginate = 10)
    {
        Gate::authorize('view-packing-list');
        AppLog::info('Packings list viewed', 'Packings loaded');
        $query = Packing::when($search !== null, function ($q) use ($search) {
            $q->bySearch($search);
        })->orderBy('name');
        return $paginate ? $query->paginate($paginate) : $query->get();
    }

    public function getPacking($id)
    {
        $packing = Packing::findOrFail($id);
        Gate::authorize('view-packing', $packing);
        AppLog::info('Packing viewed', 'Packing ' . $id . ' viewed', $packing);
        return $packing;
    }

    public function createPacking($name, $cost)
    {
        Gate::authorize('create-packing');
        try {
            AppLog::info('Packing created', 'Packing ' . $name . ' created');
            $packing = Packing::create([
                'name' => $name,
                'cost' => $cost,
                'is_active' => true,
            ]);
            return $packing;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Packing creation failed', 'Packing ' . $name . ' creation failed');
            throw new PackingManagementException('Packing creation failed');
        }
    }

    public function updatePacking(Packing $packing, $name, $cost)
    {
        Gate::authorize('update-packing', $packing);
        try {
            $packing->update([
                'name' => $name,
                'cost' => $cost,
            ]);
            AppLog::info('Packing updated', 'Packing ' . $packing->name . ' updated');
            return $packing;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Packing update failed', 'Packing ' . $packing->name . ' update failed');
            throw new PackingManagementException('Packing update failed');
        }
    }

    public function setPackingStatus(Packing $packing, $status)
    {
        Gate::authorize('update-packing', $packing);
        try {
            $packing->update(['is_active' => $status]);
            AppLog::info('Packing status set', 'Packing ' . $packing->name . ' status set to ' . $status);
            return $packing;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Packing status set failed', 'Packing ' . $packing->name . ' status set to ' . $status . ' failed');
            throw new PackingManagementException('Packing status set failed');
        }
    }

    public function deletePacking(Packing $packing)
    {
        Gate::authorize('delete-packing');
        AppLog::info('Packing deleted', 'Packing ' . $packing->name . ' deleted');
        $packing->delete();
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PackingServiceProvider::class, function ($app) {
            return new PackingServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-packing', [PackingPolicy::class, 'view']);
        Gate::define('view-packing-list', [PackingPolicy::class, 'viewAny']);
        Gate::define('create-packing', [PackingPolicy::class, 'create']);
        Gate::define('update-packing', [PackingPolicy::class, 'update']);
        Gate::define('delete-packing', [PackingPolicy::class, 'delete']);
    }
}
