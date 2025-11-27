<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;

// --- PUBLIC ROUTES (Tidak butuh token) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Auth Actions
    Route::post('/logout', [AuthController::class, 'logout']);

    // Get User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Courses CRUD
    Route::apiResource('courses', CourseController::class);

    // Material Routes
    Route::post('/materials', [App\Http\Controllers\Api\MaterialController::class, 'store']);
    Route::get('/materials', [App\Http\Controllers\Api\MaterialController::class, 'index']);
    Route::delete('/materials/{id}', [App\Http\Controllers\Api\MaterialController::class, 'destroy']);

    // Quiz Routes
    Route::apiResource('quizzes', \App\Http\Controllers\Api\QuizController::class);

    // Question Route (Hanya butuh Store dan Destroy)
    Route::post('/questions', [\App\Http\Controllers\Api\QuestionController::class, 'store']);
    Route::delete('/questions/{id}', [\App\Http\Controllers\Api\QuestionController::class, 'destroy']);

    // Student Actions
    Route::post('/submit-quiz', [\App\Http\Controllers\Api\QuizResultController::class, 'store']);
    Route::get('/my-results', [\App\Http\Controllers\Api\QuizResultController::class, 'index']);
    // progress
    Route::post('/update-progress', [\App\Http\Controllers\Api\CourseProgressController::class, 'update']);
    Route::get('/my-progress', [\App\Http\Controllers\Api\CourseProgressController::class, 'index']);

    // Certificate Routes
    Route::post('/claim-certificate', [\App\Http\Controllers\Api\CertificateController::class, 'store']);
    Route::get('/my-certificates', [\App\Http\Controllers\Api\CertificateController::class, 'index']);
});
