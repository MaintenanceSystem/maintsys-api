<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\MaintenanceLogController;
use App\Http\Controllers\Api\MachineReadingController;
use App\Http\Controllers\Api\StatusAlertController;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('machines', MachineController::class);
    Route::apiResource('service-orders', ServiceOrderController::class);
    Route::apiResource('maintenance-logs', MaintenanceLogController::class);
    Route::apiResource('machine-readings', MachineReadingController::class);
    Route::apiResource('status-alerts', StatusAlertController::class);
});