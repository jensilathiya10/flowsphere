<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoDashboardController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Demo-only browser view of the API scaffold (see DemoDashboardController).
Route::get('/dashboard-demo', [DemoDashboardController::class, 'index'])->name('demo.dashboard');
Route::post('/dashboard-demo/ask-flow-ai', [DemoDashboardController::class, 'askFlowAI'])->name('demo.ask-flow-ai');

// Session-based auth (Sanctum SPA cookies). GET routes render the
// placeholder forms; POST routes run under the default 'web' group
// so the session/CSRF cookie pair Sanctum needs is already in play.
Route::middleware('guest')->group(function () {
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/onboarding', fn () => view('onboarding'))->name('onboarding');
    Route::get('/dashboard', [DashboardController::class, 'page'])->name('dashboard');

    Route::get('/projects', [ProjectController::class, 'pageIndex'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('/projects/{project}', [ProjectController::class, 'pageShow'])->name('projects.show');
});
