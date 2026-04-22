<?php

use App\Http\Controllers\Api\V1\HeartbeatController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/users/current')
    ->middleware('api.key')
    ->group(function (): void {
        Route::post('/heartbeats', [HeartbeatController::class, 'store']);
        Route::post('/heartbeats.bulk', [HeartbeatController::class, 'bulk']);
    });
