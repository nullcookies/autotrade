<?php
chdir(__DIR__);
defined('IS_VALID') or define('IS_VALID', 1);
require_once __DIR__ . '/../../../main.php';

// Load composer
require_once LIB_DIR . '/discord/vendor/autoload.php';

global $environment;

$discord = new \Discord\Discord([
    'token' => $environment->discord->bots->{1}->token,
]);

$discord->on('ready', function ($discord) {
        echo "Bot is ready.", PHP_EOL;

        // $discord->updatePresence($game);

        $discord->on('heartbeat', function () use ($discord) {
            echo "heartbeat called at: " . time() . PHP_EOL;
        });

        $discord->on('message', function ($message) use ($discord) {
            $guild_id = $message->channel->guild_id;
            $channel_id = $message->channel_id;
            $guild = $discord->guilds->get('id', $guild_id);
            $channel = $guild->channels->get('id', $channel_id);

            $channel->sendMessage('we async now')->then(function ($message) {
                echo "The message was sent!", PHP_EOL;
            })->otherwise(function ($e) {
                echo "There was an error sending the message: {$e->getMessage()}", PHP_EOL;
                echo $e->getTraceAsString() . PHP_EOL;
            });

            echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;
        });
});

$discord->run();