<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\ServiceOrderController;
use App\Http\Controllers\Api\MaintenanceLogController;
use App\Http\Controllers\Api\MachineReadingController;
use App\Http\Controllers\Api\StatusAlertController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Só admin
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
});

// Admin e gerente
Route::middleware(['auth:sanctum', 'role:admin|gerente'])->group(function () {
    Route::apiResource('machines', MachineController::class);
    Route::apiResource('status-alerts', StatusAlertController::class);
});

// Admin, gerente e técnico
Route::middleware(['auth:sanctum', 'role:admin|gerente|tecnico'])->group(function () {
    Route::apiResource('service-orders', ServiceOrderController::class);
    Route::apiResource('maintenance-logs', MaintenanceLogController::class);
    Route::apiResource('machine-readings', MachineReadingController::class);
});

// Qualquer autenticado
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
