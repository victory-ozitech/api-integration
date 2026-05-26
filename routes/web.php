<?php

use App\Http\Controllers\User\DashBoardController;
use App\Http\Controllers\User\FacebookController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([HandleInertiaRequests::class])->group(function () {
    Route::get('/', fn() => Inertia::render('Home'));
});

Route::controller(DashBoardController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
    Route::get('/create', 'create')->name('create');
});