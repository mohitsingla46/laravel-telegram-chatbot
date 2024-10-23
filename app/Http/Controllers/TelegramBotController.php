<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    protected $telegram;
    protected $questions = [
        'askName' => 'What is your name?',
        'askEmail' => 'What is your e-mail?',
        'askFieldOfInterest' => 'What is your field of interest?',
        'askWorkEnvironment' => 'What is your preferred work environment?',
    ];

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function setWebhook()
    {
        $domain = request()->getHost();
        $baseUrl = 'https://' . $domain;
        $url = $baseUrl . '/telegram/webhook';
        $response = Telegram::setWebhook(['url' => $url]);
        return $response;
    }

    public function handleWebhook(Request $request)
    {
        $chatId = $request->input('message.chat.id');
        $text = $request->input('message.text');
        Log::info($request->session()->get('step'));
        $step = $request->session()->get('step', 'start');

        switch ($step) {
            case 'start':
                $request->session()->put('step', 'asking_name');
                $this->askName($chatId);
                break;

            case 'asking_name':
                $request->session()->put('step', 'asking_email');
                $this->askEmail($chatId);
                break;
        }
    }

    // Function to ask user's name
    protected function askName($chatId)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $this->questions['askName']
        ]);
    }

    // Function to ask user's e-mail
    protected function askEmail($chatId)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $this->questions['askEmail']
        ]);
    }

    public function test(Request $request)
    {
        // $request->session()->put('mykey', 'demo');

        $value = $request->session()->get('mykey', 'fgsgg');
        print_r($value);
    }
}
