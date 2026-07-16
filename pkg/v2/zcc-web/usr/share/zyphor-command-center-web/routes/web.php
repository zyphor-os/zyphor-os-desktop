<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// View
Route::get('/profile', [ProfileController::class, 'getUserName']);

// APIs
Route::get('/get-user-info', [ProfileController::class, 'getUserInfo']);
Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar']);
