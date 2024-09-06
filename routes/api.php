<?php

use App\Http\Controllers\ChatBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/chat-bot', [ChatBotController::class, 'listenToReplies']);
