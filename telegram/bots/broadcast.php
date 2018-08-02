<?php
/**
 * Usage on CLI: $ php broadcast.php [telegram-chat-id] [message]
 */
if (!defined('STDIN')) die('Access denied.' . "\n");

// Error handle
require_once __DIR__ . '/error-handle.php';

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

$API_KEY  = $environment->telegram->bot->{2}->token;
$BOT_NAME = $environment->telegram->bot->{2}->name;

$telegram = new Telegram($API_KEY, $BOT_NAME);

// Get the chat id and message text from the CLI parameters.
$chat_id = isset($argv[1]) ? $argv[1] : $environment->telegram->main->id;
$message = isset($argv[2]) ? $argv[2] : 'Message sending at ' . date('H:i:s d/m/Y');


// ------------------------------------------------------------ //
$messages = [];
$messages[] = "*Du Ciaolin nhớ tôi không?*";
$messages[] = "*Lượng Ciaolin nhớ tôi không?*";
$messages[] = "*Shark Nóc nhớ tôi không?*";

$chat_id = -1001178093869; //Group BítMết
$photo = 'https://i.ytimg.com/vi/wfvxTyFJOiU/maxresdefault.jpg';
sendPhoto($chat_id, $photo);
$message = $messages[rand(0, count($messages)-1)];
sendMessage($chat_id, $message);

$message = 'Giờ là ' . date('h:i A d/m/Y');
$message .= PHP_EOL . \BossBaby\Telegram::format_xbt_price_for_telegram();
$message .= PHP_EOL;
sendMessage($chat_id, $message);
// ------------------------------------------------------------ //


function sendPhoto($chat_id = '', $photo = '')
{
    if ($chat_id !== '' && $photo !== '') {
        $result = Request::sendPhoto([
            'chat_id' => $chat_id,
            'photo'   => $photo,
            'parse_mode' => 'markdown',
        ]);
    }
}

function sendMessage($chat_id = '', $message = '')
{
    if ($chat_id !== '' && $message !== '') {
        $data = [
            'chat_id' => $chat_id,
            'text'    => $message,
            'parse_mode' => 'markdown',
        ];

        $result = Request::sendMessage($data);

        if ($result->isOk()) {
            echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
        } else {
            echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
        }
    }
    else {
        die('Nothing to do!');
    }
}