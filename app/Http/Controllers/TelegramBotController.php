<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function handleWebhook()
    {
        try {
            $update = Telegram::getWebhookUpdates();

            $chatId = $update['message']['chat']['id'];
            // $text = $update['message']['text'];

            $response = "Baad mei aana! Mohit ji abhi muje develop kar rhe hai. \u{1F60A}";

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $response,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return response('OK', 200);
    }

    public function setWebhook()
    {
        $domain = request()->getHost();
        $baseUrl = 'https://' . $domain;
        $url = $baseUrl . '/telegram/webhook';
        $response = Telegram::setWebhook(['url' => $url]);
        return $response;
    }
}
