<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AspirationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'apiLogin']);

// Public feed (HANYA menampilkan aspirasi yang sudah diverifikasi & dipublikasikan)
Route::get('/aspirations', [AspirationController::class, 'index']);

// Protected endpoints (Memerlukan token login)
Route::middleware('auth:sanctum')->group(function () {
    // Siswa
    Route::get('/user/aspirations', [AspirationController::class, 'myAspirations']);
    Route::post('/aspirations', [AspirationController::class, 'store']);
    Route::post('/aspirations/{id}/upvote', [AspirationController::class, 'upvote']);

    // Admin & Guru (Moderasi publikasi, status tindak lanjut, & penghapusan)
    Route::get('/admin/aspirations', [AspirationController::class, 'adminIndex']);
    Route::put('/aspirations/{id}/publish', [AspirationController::class, 'updatePublishStatus']);
    Route::put('/aspirations/{id}/status', [AspirationController::class, 'updateStatus']);
    Route::delete('/aspirations/{id}', [AspirationController::class, 'destroy']);
});
