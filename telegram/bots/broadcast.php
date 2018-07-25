<?php
/**
 * Usage on CLI: $ php broadcast.php [telegram-chat-id] [message]
 */

chdir(__DIR__);

// Error handle
require_once(__DIR__ . "/error-handle.php");

defined('IS_VALID') or define('IS_VALID', 1);
require_once("../../../main.php");

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

$API_KEY  = $environment->token;
$BOT_NAME = $environment->user_name;

$telegram = new Telegram($API_KEY, $BOT_NAME);

// Get the chat id and message text from the CLI parameters.
$chat_id = isset($argv[1]) ? $argv[1] : $environment->my_id;
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