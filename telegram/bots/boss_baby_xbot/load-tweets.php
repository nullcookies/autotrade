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

$shown_tweets_file = LOGS_DIR . '/shown_tweets.php';
$shown_tweets = (is_file($shown_tweets_file) and file_exists($shown_tweets_file)) ? \BossBaby\Config::read($shown_tweets_file) : [];

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
        $chat_id   = $environment->telegram->channels->{4}->id;
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

            $latest_tweet = \BossBaby\Telegram::get_user_feeds($username, 5);
            // $latest_tweet = \BossBaby\Twitter::get_user_feeds($username, 1);
            // \BossBaby\Utility::writeLog('latest_tweet:'.serialize($latest_tweet));
            // dump($latest_tweet);

            $old_one = (isset($shown_tweets[$coin]) and trim($shown_tweets[$coin])) ? trim($shown_tweets[$coin]) : '';
            $first_one = (isset($latest_tweet[0]) and trim($latest_tweet[0])) ? trim($latest_tweet[0]) : '';
            if ($first_one !== $old_one) {
                $data['text'] = $shown_tweets[$coin] = $first_one;
            }

            // dump($data['text']);die;
            
            // if (trim($data['text'])) {
            //     // \BossBaby\Utility::writeLog('text:'.serialize($data['text']));
            //     $result = Longman\TelegramBot\Request::sendMessage($data);
            //     // dump('$result'); dump($result);
            //     // sleep(1);

            //     // if ($result->isOk()) {
            //     //     echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
            //     // } else {
            //     //     echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
            //     // }

            //     // Send message to Discord
            //     $webhook_url = $environment->discord->bots->{9}->webhook_url;
            //     $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
            //     // \BossBaby\Utility::writeLog(__FILE__.'result2:'.serialize($result));
            //     // sleep(1);
            // }
        }

        // Write back data into cache
        \BossBaby\Config::write($shown_tweets_file, (array) $shown_tweets);
        sleep(1);

        dump($shown_tweets);die;

        // return Request::emptyResponse();
    
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
