<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function handleWebhook()
    {
        try {
            $updates = Telegram::getWebhookUpdates();

            if ($updates->has('message')) {
                $message = $updates->getMessage();
                $chatId = $message->getChat()->getId();
                $text = $message->getText();

                // Call the function to send inline keyboard if user sends a specific command
                if ($text == '/start') {
                    $this->sendQuestionWithInlineKeyboard($chatId);
                }
            } elseif ($updates->has('callback_query')) {
                // Handle the response from the inline keyboard
                $callbackQuery = $updates->get('callback_query');
                $data = $callbackQuery['data'];
                $chatId = $callbackQuery['message']['chat']['id'];

                // You can now handle responses based on the $data (e.g., user choices)
                if ($data == 'Pizza') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'You selected Pizza!'
                    ]);
                } else if($data == 'Burger') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'You selected Burger!'
                    ]);
                } else {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'Invalid Option!'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return response('OK', 200);
    }

    function sendQuestionWithInlineKeyboard($chatId)
    {
        // Define the inline keyboard
        $replyMarkup = json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'Pizza', 'callback_data' => 'Pizza'],
                    ['text' => 'Burger', 'callback_data' => 'Burger']
                ]
            ]
        ]);

        // Send the message with the inline keyboard
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Please choose an option:',
            'reply_markup' => $replyMarkup
        ]);
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
