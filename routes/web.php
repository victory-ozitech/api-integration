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
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/index', 'index')->name('posts.index');
    Route::get('/create', 'create')->name('posts.create');
    Route::get('/{id}/edit', 'edit')->name('posts.edit');
});

Route::controller(FacebookController::class)->group(function () {
    Route::get('/connect-facebook', 'connect')->name('facebook.connect');
    Route::get('/auth/facebook', 'redirectToFacebook')->name('facebook.redirect');
    Route::get('/auth/facebook/callback', 'handleFacebookCallback')->name('facebook.callback');
    Route::get('/facebook/pages', 'pages')->name('facebook.page');
    Route::post('/facebook/publish/{post}', 'publishNow')->name('facebook.publish');
    Route::post('/facebook/schedule-post', 'schedulePost')->name('facebook.schedule-post');
});