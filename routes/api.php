<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// LocalMod Moderation Routes (Automatically prefixed with /api)
Route::get('/moderate/test', [CommentController::class, 'test']);
Route::post('/comments', [CommentController::class, 'store']);