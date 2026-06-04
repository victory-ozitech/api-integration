<?php

use App\Http\Controllers\User\DashBoardController;
use App\Http\Controllers\Facebook\FacebookAuthController;
use App\Http\Controllers\Facebook\FacebookPostController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([HandleInertiaRequests::class])->group(function () {
    Route::get('/', fn() => Inertia::render('Home'));
});

Route::controller(DashBoardController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/index', 'index')->name('posts.index');
    Route::get('/create', 'create')->name('posts.create');
    Route::get('/{post}/edit', 'edit')->name('posts.edit');
});

// Facebook Auth
Route::controller(FacebookAuthController::class)->group(function () {
    Route::get('/connect-facebook', 'connect')->name('facebook.connect');
    Route::get('/auth/facebook', 'redirectToFacebook')->name('facebook.redirect');
    Route::get('/auth/facebook/callback', 'handleCallback')->name('facebook.callback');
    Route::post('/auth/facebook/select-page', 'selectedPage')->name('facebook.select-page');
});

// Posts
Route::controller(FacebookPostController::class)->group(function () {
    Route::get('/posts', 'index')->name('posts.index');
    Route::post('/posts', 'store')->name('posts.store');
    Route::put('/posts/{post}', 'update')->name('posts.update');
    Route::delete('/posts/{post}', 'destroy')->name('posts.destroy');
});