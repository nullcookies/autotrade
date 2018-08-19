<?php
/**
 * README
 * This configuration file is intended to run a list of commands with crontab.
 * Uncommented parameters must be filled
 */

if (!defined('STDIN')) die('Access denied.' . "\n");

// Error handle
require_once __DIR__ . '/../error-handle.php';

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

run_cron();
$sleep = 10;
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();

function run_cron() {
    // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

    // Get global config
    global $environment;

    // Add you bot's API key and name
    $bot_api_key  = $environment->telegram->bots->{2}->token;
    $bot_username = $environment->telegram->bots->{2}->username;
    $telegram = new Telegram($bot_api_key, $bot_username);
    
    // $chat_id   = $message->getChat()->getId();
    $chat_id   = $environment->telegram->channels->{3}->id;
    // $chat_id   = $environment->telegram->main->id;

    $data = [
        'chat_id'    => $chat_id,
        'parse_mode' => 'html',
        'disable_web_page_preview' => true,
        'text' => '',
    ];

    // $data['text'] = 'Message at ' . date('H:i:s d/m/Y');

    $list_coin_bittrex = \BossBaby\Telegram::get_coin_pulse_bittrex(-5, 5);
    // \BossBaby\Utility::writeLog('list_coin_bittrex:'.serialize($list_coin_bittrex));
    
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

    // Format for Telegram
    if ($list_coin_bittrex['telegram']) {
        foreach ($list_coin_bittrex['telegram'] as $text) {
            $data['text'] = trim($text);

            // $result = Longman\TelegramBot\Request::sendMessage($data);
            $result = Request::sendMessage($data);
            // dump('$result'); dump($result);
            // \BossBaby\Utility::writeLog('result:'.serialize($result));

            // if ($result->isOk()) {
            //     // echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
            //     \BossBaby\Utility::writeLog('result:Message sent succesfully to: ' . $chat_id);
            // } else {
            //     // echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
            //     \BossBaby\Utility::writeLog('result:Sorry message not sent to: ' . $chat_id);
            // }
            sleep(1);
        }
    }

    // Format for Discord
    if ($list_coin_bittrex['discord']) {
        foreach ($list_coin_bittrex['discord'] as $text) {
            $data['text'] = trim($text);

            // Send message to Discord
            $webhook_url = $environment->discord->bots->{2}->webhook_url;
            $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
            // \BossBaby\Utility::writeLog(__FILE__.'result2:'.serialize($result));
            sleep(1);
        }
    }
}
