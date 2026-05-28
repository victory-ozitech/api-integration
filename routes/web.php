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
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/index', 'index')->name('posts.index');
    Route::get('/create', 'create')->name('posts.create');
    Route::get('/{id}/edit', 'edit')->name('posts.edit');
});