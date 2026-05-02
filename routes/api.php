<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IssueController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Public ───────────────────────────────────────────────────────────────
    Route::post('/login', [AuthController::class, 'login']);

    // ─── Authenticated ────────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Escalated (before resource to avoid route collision)
        Route::get('/issues/escalated', [IssueController::class, 'escalated']);

        // Issue resource
        Route::apiResource('issues', IssueController::class);

        // Regenerate AI summary
        Route::post('/issues/{issue}/regenerate-summary', [IssueController::class, 'regenerateSummary']);
    });
});