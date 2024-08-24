<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BrandController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

if (App::environment('local')) {
    Route::get('/clear-all', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Auth::logout();

        return redirect('/');
    });
}

Route::get('auth/redirect/{provider}', [SocialiteController::class, 'redirectToProvider'])->name('auth.redirect');
Route::get('auth/callback/{provider}', [SocialiteController::class, 'handleProviderCallback'])->name('auth.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::resources([
        'brands' => BrandController::class,
    ]);
});
