<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Handle preflight OPTIONS request
Route::options('{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');

// Route test
Route::get('/test', function () {
    return response()->json([
        'message' => 'API Laravel berhasil terhubung!',
        'status' => 'success',
        'timestamp' => now()
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
