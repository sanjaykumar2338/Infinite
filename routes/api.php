<?php

use App\Http\Controllers\Api\AccessController;
use App\Http\Controllers\Api\AnalyzeController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\CallController;
use App\Http\Controllers\Api\ExtensionHeartbeatController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\MeetingFinalizeController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', StripeWebhookController::class);

Route::middleware('firebase.auth')->group(function () {
    Route::get('/me', MeController::class);
    Route::get('/access/check', AccessController::class);
    Route::post('/analyze', AnalyzeController::class);
    Route::post('/meetings/finalize', MeetingFinalizeController::class);
    Route::post('/call/start', [CallController::class, 'start']);
    Route::post('/call/usage', [CallController::class, 'usage']);
    Route::post('/extension/heartbeat', ExtensionHeartbeatController::class);
    Route::post('/billing/checkout/{plan}', [BillingController::class, 'checkout'])
        ->whereIn('plan', ['spark', 'forge']);
});
