<?php

namespace App\Providers;

use App\Exceptions\UserManagementException;
use App\Models\AppLog;
use App\Models\User;
use App\Policies\UserPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;


class UserServiceProvider extends ServiceProvider
{

    public function getUser($id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('view-user', $user);
        AppLog::info('User viewed', 'User ' . $user->name . ' viewed', $user);
        return $user;
    }


    public function getUsers($status = null, $role = null, $paginate = false)
    {
        Gate::authorize('view-user-list', User::class);
        $usersQuery = User::when($status !== null, function ($query) use ($status) {
            return $query->byStatus($status);
        })->when($role !== null, function ($query) use ($role) {
            return $query->byRole($role);
        });

        if ($paginate) {
            return $usersQuery->paginate();
        }
        AppLog::info('User list viewed', 'User list viewed', $usersQuery);
        return $usersQuery->get();
    }

    public function createUser($username, $name, $password, $role = User::ROLE_USER)
    {

        Gate::authorize('create-user', User::class);

        if (User::where('username', $username)->exists()) {
            throw new UserManagementException('User already exists');
        }

        try {
            $user = User::create([
                'username' => $username,
                'name' => $name,
                'password' => Hash::make($password),
                'role' => $role,
            ]);
            AppLog::info('User created', 'User ' . $user->name . ' created', $user);
            return $user;
        } catch (Exception $e) {
            report($e);
            throw new UserManagementException('Failed to create user');
        }
    }


    public function updateUser(User $user, $username, $name, $role = User::ROLE_USER)
    {
        Gate::authorize('update-user', $user);

        try {
            $user->update([
                'username' => $username,
                'name' => $name,
                'role' => $role
            ]);
            AppLog::info('User updated', 'User ' . $user->name . ' updated', $user);
            return $user;
        } catch (Exception $e) {
            report($e);
            throw new UserManagementException('Failed to update user');
        }
    }

    public function setUserStatus(User $user, $status)
    {
        Gate::authorize('update-user', $user);
        try {
            $user->update(['is_active' => $status]);
            AppLog::info('User status updated', 'User ' . $user->name . ' status updated to ' . $status, $user);
            return $user;
        } catch (Exception $e) {
            report($e);
            throw new UserManagementException('Failed to update user status');
        }
    }


    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-user', [UserPolicy::class, 'view']);
        Gate::define('view-user-list', [UserPolicy::class, 'viewAny']);
        Gate::define('create-user', [UserPolicy::class, 'create']);
        Gate::define('update-user', [UserPolicy::class, 'update']);
        Gate::define('deactivate-user', [UserPolicy::class, 'deactivate']);
    }
}
