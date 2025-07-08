<?php

use App\Livewire\Auth\Login;
use App\Livewire\Clients\ClientIndex;
use App\Livewire\Clients\ClientShow;
use App\Livewire\Products\ProductsIndex;
use App\Livewire\Products\ProductsShow;
use App\Livewire\Settings\AppLogIndex;
use App\Livewire\Settings\CurrenciesIndex;
use App\Livewire\Settings\PackingsIndex;
use App\Livewire\Settings\SpecsIndex;
use App\Livewire\Settings\UsersIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => ['auth']], function () {
    // Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/clients', ClientIndex::class)->name('clients.index');
    Route::get('/clients/{client_id}', ClientShow::class)->name('clients.show');
    
    Route::get('/settings/products', ProductsIndex::class)->name('products.index');
    Route::get('/settings/products/{product_id}', ProductsShow::class)->name('products.show');

    Route::get('/settings/users', UsersIndex::class)->name('users.index');
    Route::get('/settings/currencies', CurrenciesIndex::class)->name('currencies.index');
    Route::get('/settings/packings', PackingsIndex::class)->name('packings.index');
    Route::get('/settings/specs', SpecsIndex::class)->name('specs.index');

    Route::get('/', UsersIndex::class)->name('users.index');
    Route::get('/settings/app-logs', AppLogIndex::class)->name('app-logs');

    Route::get('/logout', function () {
    Auth::logout();
        return redirect('/login');
    });
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');
});
