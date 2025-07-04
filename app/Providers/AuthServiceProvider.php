<?php

namespace App\Providers;

use App\Exceptions\AppException;
use App\Models\AppLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthServiceProvider extends ServiceProvider
{

    public function login($username, $password) : User
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }
        if (!Hash::check($password, $user->password)) {
            throw new UnauthorizedException('Invalid credentials');
        }
        Auth::login($user);
        AppLog::info('User logged in', 'User ' . $user->name . ' logged in', $user);
        return $user;
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthServiceProvider::class, function ($app) {
            return new AuthServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
 
    }
}
