<?php
/**
 * README
 * This file is intended to set the webhook.
 * Uncommented parameters must be filled
 */

// Error handle
require_once(__DIR__ . "/../error-handle.php");

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

// Add you bot's API key and name
$bot_api_key  = $environment->telegram->bot->{2}->token;
$bot_username = $environment->telegram->bot->{2}->username;

// Define the URL to your hook.php file
$hook_url     = $environment->telegram->bot->{2}->root_url . 'hook.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url, array(
        'max_connections' => $environment->telegram->bot->{2}->max_connections
    ));

    // To use a self-signed certificate, use this line instead
    //$result = $telegram->setWebhook($hook_url, ['certificate' => $certificate_path]);

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
}
