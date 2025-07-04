<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');
});
