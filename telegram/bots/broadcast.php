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

if ($chat_id !== '' && $message !== '') {
    $data = [
        'chat_id' => $chat_id,
        'text'    => $message,
    ];

    $result = Request::sendMessage($data);

    if ($result->isOk()) {
        echo 'Message sent succesfully to: ' . $chat_id;
    } else {
        echo 'Sorry message not sent to: ' . $chat_id;
    }
}