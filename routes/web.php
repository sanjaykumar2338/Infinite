<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BillingCheckoutController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'show'])->defaults('page', 'home')->name('home');
Route::get('/pricing', [PageController::class, 'show'])->defaults('page', 'pricing')->name('pricing');
Route::get('/spark', [PageController::class, 'show'])->defaults('page', 'spark')->name('spark');
Route::get('/forge', [PageController::class, 'show'])->defaults('page', 'forge')->name('forge');
Route::get('/reports', [PageController::class, 'show'])->defaults('page', 'reports')->name('reports.showcase');
Route::get('/privacy-policy', [PageController::class, 'show'])->defaults('page', 'privacy')->name('privacy');
Route::get('/terms-and-conditions', [PageController::class, 'show'])->defaults('page', 'terms')->name('terms');

Route::get('/login', [UserAuthController::class, 'create'])->name('login');
Route::get('/signup', [UserAuthController::class, 'create'])->name('signup');
Route::post('/login/firebase', [UserAuthController::class, 'firebaseSession'])->name('login.firebase');
Route::post('/logout', [UserAuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/dashboard', UserDashboardController::class)->middleware('auth')->name('dashboard');
Route::get('/billing/checkout/{plan}', BillingCheckoutController::class)
    ->whereIn('plan', ['spark', 'forge'])
    ->name('billing.checkout');

Route::get('/adnin', [AuthController::class, 'create'])->name('admin.login');
Route::post('/adnin', [AuthController::class, 'store'])->name('admin.login.store');
Route::redirect('/admin/login', '/adnin');
Route::post('/admin/logout', [AuthController::class, 'destroy'])->name('admin.logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::delete('/reports/{type}/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
});
