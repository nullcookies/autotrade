<?php
chdir(__DIR__);
defined('IS_VALID') or define('IS_VALID', 1);
require_once __DIR__ . '/../../../main.php';

// Load composer
require_once LIB_DIR . '/discord/vendor/autoload.php';

// Load global config
global $environment;

$discord = new \Discord\Discord([
    'token' => $environment->discord->bots->{1}->token,
]);

$discord->on('ready', function ($discord) {
    echo "Bot is ready.", PHP_EOL;
  
    // Listen for events here
    $discord->on('message', function ($message) {
        echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;
    });
});

$discord->run();