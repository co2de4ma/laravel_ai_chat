<?php

use App\Http\Controllers\AiChatTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AiChatTestController::class, 'index']);
Route::get('/ai-chat-test', [AiChatTestController::class, 'index'])->name('ai-chat-test.index');
Route::post('/ai-chat-test', [AiChatTestController::class, 'store'])->name('ai-chat-test.store');
