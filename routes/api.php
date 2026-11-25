<?php

use App\Http\Controllers\Api\DeviceController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function (): JsonResponse {
    return response()->json(['status' => 'ok']);
});

// get
Route::get('/devices', [DeviceController::class, 'index']);
Route::get('/devices/{device:uuid}', [DeviceController::class, 'show'])
    ->missing(fn (): JsonResponse => response()->json([
        'message' => 'Device not found.',
    ], JsonResponse::HTTP_NOT_FOUND));

// post
Route::post('/devices', [DeviceController::class, 'create']);
Route::post('/devices/{uuid}/ping', [DeviceController::class, 'ping']);
