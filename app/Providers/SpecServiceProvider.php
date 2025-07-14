<?php

namespace App\Providers;

use App\Exceptions\SpecManagementException;
use App\Models\AppLog;
use App\Models\Spec;
use App\Policies\SpecPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SpecServiceProvider extends ServiceProvider
{

    public function getSpecs($search = null, $paginate = 10, $forDropdown = false)
    {
        if (!$forDropdown) {
            Gate::authorize('view-spec-list');
        }
        $query = Spec::query();
        if ($search) {
            $query->bySearch($search);
        }
        if (!$forDropdown) {
            AppLog::info('Specs list viewed', 'Specs loaded');
        }
        return $paginate ? $query->paginate($paginate) : $query->get();
    }

    public function getSpec($id)
    {
        $spec = Spec::findOrFail($id);
        AppLog::info('Spec viewed', 'Spec ' . $id . ' viewed');
        Gate::authorize('view-spec', $spec);
        return $spec;
    }

    public function createSpec($name)
    {
        Gate::authorize('create-spec');
        try {
            $spec = Spec::create(['name' => $name]);
            AppLog::info('Spec created', 'Spec ' . $name . ' created');
            return $spec;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Spec creation failed', 'Spec ' . $name . ' creation failed');
            throw new SpecManagementException('Spec creation failed');
        }
    }

    public function updateSpec(Spec $spec, $name)
    {
        Gate::authorize('update-spec', $spec);
        try {
            $spec->update(['name' => $name]);
            AppLog::info('Spec updated', 'Spec ' . $name . ' updated');
            return $spec;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Spec update failed', 'Spec ' . $name . ' update failed');
            throw new SpecManagementException('Spec update failed');
        }
    }

    public function deleteSpec(Spec $spec)
    {
        Gate::authorize('delete-spec', $spec);
        if ($spec->products()->count() > 0) {
            throw new SpecManagementException('Spec has products');
        }
        try {
            $spec->delete();
            AppLog::info('Spec deleted', 'Spec ' . $spec->name . ' deleted');
            return $spec;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Spec deletion failed', 'Spec ' . $spec->name . ' deletion failed');
            throw new SpecManagementException('Spec deletion failed');
        }
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SpecServiceProvider::class, function ($app) {
            return new SpecServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-spec-list', [SpecPolicy::class, 'viewAny']);
        Gate::define('view-spec', [SpecPolicy::class, 'view']);
        Gate::define('create-spec', [SpecPolicy::class, 'create']);
        Gate::define('update-spec', [SpecPolicy::class, 'update']);
        Gate::define('delete-spec', [SpecPolicy::class, 'delete']);
    }
}
