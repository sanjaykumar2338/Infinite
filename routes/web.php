<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteContentController;
use App\Http\Controllers\BillingCheckoutController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserReportController;
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
Route::get('/dashboard/reports/{report}', [UserReportController::class, 'showReport'])
    ->middleware('auth')
    ->name('dashboard.reports.show');
Route::get('/dashboard/charts/{reportChart}', [UserReportController::class, 'showChart'])
    ->middleware('auth')
    ->name('dashboard.charts.show');
Route::get('/dashboard/badges/{badgeReport}', [UserReportController::class, 'showBadge'])
    ->middleware('auth')
    ->name('dashboard.badges.show');
Route::get('/extension', [ExtensionController::class, 'show'])->middleware('auth')->name('extension.show');
Route::get('/extension/download', [ExtensionController::class, 'download'])->middleware('auth')->name('extension.download');
Route::match(['get', 'post'], '/billing/checkout/{plan}', BillingCheckoutController::class)
    ->whereIn('plan', ['spark', 'forge'])
    ->name('billing.checkout');

Route::get('/adnin', [AuthController::class, 'create'])->name('admin.login');
Route::post('/adnin', [AuthController::class, 'store'])->name('admin.login.store');
Route::redirect('/admin/login', '/adnin');
Route::post('/admin/logout', [AuthController::class, 'destroy'])->name('admin.logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::delete('/reports/{type}/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/website-content', [WebsiteContentController::class, 'index'])->name('website-content.index');
    Route::get('/website-content/{pageContent}/edit', [WebsiteContentController::class, 'edit'])->name('website-content.edit');
    Route::patch('/website-content/{pageContent}', [WebsiteContentController::class, 'update'])->name('website-content.update');
});
