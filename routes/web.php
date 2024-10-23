<?php

use App\Http\Controllers\TelegramBotController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/telegram/webhook', [TelegramBotController::class, 'handleWebhook']);

Route::get('/set-webhook', [TelegramBotController::class, 'setWebhook']);
