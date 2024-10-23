<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    // Handle Webhook
    public function handleWebhook(Request $request)
    {
        $updates = $request->all();

        // Check if the message contains text
        if (isset($updates['message']['text'])) {
            $chatId = $updates['message']['chat']['id'];
            $text = $updates['message']['text'];

            // Call functions based on user input
            if ($text == '/start') {
                $this->sendStartMessage($chatId);
            } else if ($text == 'Start') {
                $this->sendQuestion($chatId);
            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Invalid Input!'
                ]);
            }
        } elseif (isset($updates['callback_query'])) {
            $this->handleCallbackQuery($updates['callback_query']);
        }

        return response('OK', 200);
    }

    // Send start message with custom keyboard
    protected function sendStartMessage($chatId)
    {
        $keyboard = json_encode([
            'keyboard' => [
                [['text' => 'Start']],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Please press Start to begin.',
            'reply_markup' => $keyboard
        ]);
    }

    protected function sendQuestion($chatId)
    {
        $inlineKeyboard = json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'Engineering', 'callback_data' => 'engineering'],
                    ['text' => 'Medical', 'callback_data' => 'medical']
                ]
            ]
        ]);

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Please select your field of interest:',
            'reply_markup' => $inlineKeyboard
        ]);
    }

    // Handle user responses (callback queries)
    protected function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        if ($data == 'engineering') {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'You chose Engineering!'
            ]);
        } elseif ($data == 'medical') {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'You chose Medical!'
            ]);
        }
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
