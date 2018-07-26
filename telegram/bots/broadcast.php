<?php
if (!defined('STDIN')) die('Access denied.' . "\n");

/**
 * Usage on CLI: $ php broadcast.php [telegram-chat-id] [message]
 */
// Error handle
require_once(__DIR__ . "/error-handle.php");

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

$API_KEY  = $environment->telegram->bot->{3}->token;
$BOT_NAME = $environment->telegram->bot->{3}->name;

$telegram = new Telegram($API_KEY, $BOT_NAME);

// Get the chat id and message text from the CLI parameters.
$chat_id = isset($argv[1]) ? $argv[1] : $environment->telegram->main->id;
$message = isset($argv[2]) ? $argv[2] : 'Message at ' . date('H:i:s d/m/Y');

$messages = [];
$messages[] = "*Du Ciaolin nhớ tôi không?*";
$messages[] = "*Lượng Ciaolin nhớ tôi không?*";
$messages[] = "*Shark Nóc nhớ tôi không?*";

$chat_id = -1001178093869; //Group BítMết
$message = $messages[rand(0, count($messages)-1)];


if ($chat_id !== '' && $message !== '') {
    $result = Request::sendPhoto([
        'chat_id' => $chat_id,
        'parse_mode' => 'markdown',
        'photo'   => 'https://i.ytimg.com/vi/wfvxTyFJOiU/maxresdefault.jpg',
    ]);

    $data = [
        'chat_id' => $chat_id,
        'text'    => $message,
        'parse_mode' => 'markdown',
    ];

    $result = Request::sendMessage($data);

    if ($result->isOk()) {
        echo 'Message sent succesfully to: ' . $chat_id;
    } else {
        echo 'Sorry message not sent to: ' . $chat_id;
    }
}