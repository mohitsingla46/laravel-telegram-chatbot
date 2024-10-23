<?php

use App\Http\Controllers\TelegramBotController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/set-webhook', [TelegramBotController::class, 'setWebhook']);

Route::post('/telegram/webhook', [TelegramBotController::class, 'handleWebhook']);

Route::get('test', [TelegramBotController::class, 'test']);
