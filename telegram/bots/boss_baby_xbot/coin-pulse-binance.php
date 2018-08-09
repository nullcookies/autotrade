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

run_cron();
$sleep = 10;
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();

function run_cron() {
    // dump(__FUNCTION__ . '::' . time());
    \BossBaby\Utility::writeLog('----'.__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

    global $environment;

    // Add you bot's API key and name
    $bot_api_key  = $environment->telegram->bots->{2}->token;
    $bot_username = $environment->telegram->bots->{2}->username;

    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
        
        // Logging (Error, Debug and Raw Updates)
        Longman\TelegramBot\TelegramLog::initErrorLog(LOGS_DIR . "/{$bot_username}_error.log");
        // Longman\TelegramBot\TelegramLog::initDebugLog(LOGS_DIR . "/{$bot_username}_debug.log");
        Longman\TelegramBot\TelegramLog::initUpdateLog(LOGS_DIR . "/{$bot_username}_update.log");

        // $chat_id   = $message->getChat()->getId();
        $chat_id   = $environment->telegram->channels->{2}->id;
        // $chat_id   = $environment->telegram->main->id;

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'text' => '',
        ];

        // $data['text'] = 'Message at ' . date('H:i:s d/m/Y');

        $list_coin_binance = \BossBaby\Telegram::get_coin_pulse_binance(-5, 5);
        // \BossBaby\Utility::writeLog('list_coin_binance:'.serialize($list_coin_binance));

        // if ($list_coin_binance)
        //     $data['text'] .= trim($list_coin_binance);

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
        if ($list_coin_binance['telegram']) {
            foreach ($list_coin_binance['telegram'] as $text) {
                $data['text'] = trim($text);

                $result = Longman\TelegramBot\Request::sendMessage($data);
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
        if ($list_coin_binance['discord']) {
            foreach ($list_coin_binance['discord'] as $text) {
                $data['text'] = trim($text);

                // Send message to Discord
                $webhook_url = $environment->discord->bots->{1}->webhook_url;
                $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
                // \BossBaby\Utility::writeLog(__FILE__.'result2:'.serialize($result));
                sleep(1);
            }
        }

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
