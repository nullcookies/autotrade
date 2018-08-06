<?php
/**
 * README
 * This configuration file is intended to run a list of commands with crontab.
 * Uncommented parameters must be filled
 */

// if (!defined('STDIN')) die('Access denied.' . "\n");

// Error handle
require_once __DIR__ . '/../error-handle.php';

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

// File store twitter data
$twitter_config = ROOT_DIR . '/config-twitter.php';
$twitter_data = \BossBaby\Config::read($twitter_config);

if (!$twitter_data) {
    \BossBaby\Utility::writeLog(__FILE__ . '::Empty config');
    exit;
}

$sleep = 9;
run_cron();
// sleep($sleep); run_cron();
// sleep($sleep); run_cron();
// sleep($sleep); run_cron();
// sleep($sleep); run_cron();

function run_cron() {
    // dump(__FUNCTION__ . '::' . time());
    // \BossBaby\Utility::writeLog('----'.__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

    global $environment;
    global $twitter_data;

    // Add you bot's API key and name
    $bot_api_key  = $environment->telegram->bots->{2}->token;
    $bot_username = $environment->telegram->bots->{2}->username;

    // Define all paths for your custom commands in this array (leave as empty array if not used)
    $commands_paths = [
       __DIR__ . '/Commands/',
    ];

    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
        
        // Logging (Error, Debug and Raw Updates)
        Longman\TelegramBot\TelegramLog::initErrorLog(LOGS_DIR . "/{$bot_username}_error.log");
        // Longman\TelegramBot\TelegramLog::initDebugLog(LOGS_DIR . "/{$bot_username}_debug.log");
        Longman\TelegramBot\TelegramLog::initUpdateLog(LOGS_DIR . "/{$bot_username}_update.log");

        // $chat_id   = $message->getChat()->getId();
        $chat_id   = $environment->telegram->channels->{3}->id;
        // $chat_id   = $environment->telegram->main->id;

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'html',
            'text' => '',
        ];

        // $data['text'] = 'Message at ' . date('H:i:s d/m/Y');

        foreach ($twitter_data as $coin => $twitter_item)
        {
            $username = trim($twitter_item['username']);
            if (!$username) continue;

            $latest_tweet = \BossBaby\Telegram::get_user_feeds($username, 1);
            // $latest_tweet = \BossBaby\Twitter::get_user_feeds($username, 1);
            // \BossBaby\Utility::writeLog('latest_tweet:'.serialize($latest_tweet));

            dump($latest_tweet);
            
            // if ($list_coin_bittrex)
            //     $data['text'] .= trim($list_coin_bittrex);

            // // dump($data['text']);die;
            
            // if (trim($data['text'])) {
            //     // \BossBaby\Utility::writeLog('text:'.serialize($data['text']));
            //     $result = Longman\TelegramBot\Request::sendMessage($data);
            //     // dump('$result'); dump($result);

            //     // if ($result->isOk()) {
            //     //     echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
            //     // } else {
            //     //     echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
            //     // }
            // }

            // if ($list_coin_bittrex['telegram']) {
            //     foreach ($list_coin_bittrex['telegram'] as $text) {
            //         $data['text'] = trim($text);

            //         $result = Longman\TelegramBot\Request::sendMessage($data);
            //         // dump('$result'); dump($result);
            //         // \BossBaby\Utility::writeLog('result:'.serialize($result));

            //         // if ($result->isOk()) {
            //         //     // echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
            //         //     \BossBaby\Utility::writeLog('result:Message sent succesfully to: ' . $chat_id);
            //         // } else {
            //         //     // echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
            //         //     \BossBaby\Utility::writeLog('result:Sorry message not sent to: ' . $chat_id);
            //         // }
            //     }
            // }

            // if ($list_coin_bittrex['discord']) {
            //     foreach ($list_coin_bittrex['discord'] as $text) {
            //         $data['text'] = trim($text);

            //         // Send message to Discord
            //         $webhook_url = $environment->discord->bots->{2}->webhook_url;
            //         $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
            //         // \BossBaby\Utility::writeLog(__FILE__.'result2:'.serialize($result));
            //     }
            // }
        }

        return Request::emptyResponse();
    
    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // dump('TelegramException:'); dump($e);
        // Silence is golden!
        // echo $e;
        // Log telegram errors
        Longman\TelegramBot\TelegramLog::error($e);
    } catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
        // Silence is golden!
        // Uncomment this to catch log initialisation errors
        // echo $e;
        // dump('TelegramLogException:'); dump($e);
    }
}
