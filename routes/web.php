<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IssueController;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ─── Auth Routes ──────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', fn() => redirect()->route('issues.index'));

    // Issues
    Route::resource('issues', IssueController::class);

    // Escalated issues (admin/moderator)
    Route::get('/escalated', [IssueController::class, 'escalated'])->name('issues.escalated');

    // AI summary regeneration
    Route::post('/issues/{issue}/regenerate-summary', [IssueController::class, 'regenerateSummary'])
        ->name('issues.regenerate-summary');
});