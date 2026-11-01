<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AspirationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'apiLogin']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'apiLogin']);

Route::get('/aspirations', [AspirationController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/aspirations', [AspirationController::class, 'store']);
    Route::post('/aspirations/{id}/upvote', [AspirationController::class, 'upvote']);
});