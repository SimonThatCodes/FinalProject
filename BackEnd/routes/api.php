<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AdoptionController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\GoogleAuthController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Google OAuth routes
Route::get('/auth/google/url', [GoogleAuthController::class, 'getAuthUrl']);
Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Pet routes
    Route::get('/pets', [PetController::class, 'index']);
    Route::get('/pets/my-pets', [PetController::class, 'myPets']);
    Route::post('/pets', [PetController::class, 'store']);
    Route::get('/pets/{id}', [PetController::class, 'show']);
    Route::put('/pets/{id}', [PetController::class, 'update']);
    Route::delete('/pets/{id}', [PetController::class, 'destroy']);

    // Chat routes
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{id}', [ChatController::class, 'show']);

    // Message routes
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/chats/{chatId}/messages', [MessageController::class, 'getMessages']);

    // Adoption routes
    Route::get('/adoptions', [AdoptionController::class, 'index']);
    Route::post('/adoptions', [AdoptionController::class, 'store']);
    Route::get('/adoptions/{id}', [AdoptionController::class, 'show']);
    Route::put('/adoptions/{id}', [AdoptionController::class, 'update']);

    // Verification routes
    Route::post('/verify', [VerificationController::class, 'verify']);
    Route::get('/verify/status', [VerificationController::class, 'checkStatus']);
    Route::post('/admin/verify/{userId}', [VerificationController::class, 'adminVerify']);
});

