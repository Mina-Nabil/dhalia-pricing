<?php

use App\Livewire\Auth\Login;
use App\Livewire\Settings\AppLogIndex;
use App\Livewire\Settings\UsersIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => ['auth']], function () {
    // Route::get('/', Dashboard::class)->name('dashboard');


    Route::get('/', UsersIndex::class)->name('users.index');
    Route::get('/settings/users', UsersIndex::class)->name('users.index');
    Route::get('/settings/app-logs', AppLogIndex::class)->name('app-logs');

    Route::get('/logout', function () {
    Auth::logout();
        return redirect('/login');
    });
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');
});
