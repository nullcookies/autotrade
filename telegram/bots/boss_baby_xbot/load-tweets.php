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

if (date('i') % 5 == 0)
    run_cron();
// $sleep = 9;
// sleep($sleep); run_cron();
// sleep($sleep); run_cron();
// sleep($sleep); run_cron();
// sleep($sleep); run_cron();

function run_cron() {
    // dump(__FUNCTION__ . '::' . time());
    // \BossBaby\Utility::writeLog('----'.__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

    global $environment;
    
    // File store twitter data
    $twitter_config = ROOT_DIR . '/config-twitter.php';
    $twitter_data = \BossBaby\Config::read($twitter_config);

    if (!$twitter_data) {
        \BossBaby\Utility::writeLog(__FILE__ . '::Empty config');
        exit;
    }

    $shown_tweets_file = LOGS_DIR . '/shown_tweets.php';
    $shown_tweets = (is_file($shown_tweets_file) and file_exists($shown_tweets_file)) ? \BossBaby\Config::read($shown_tweets_file) : [];

    $list_filter = (array) $environment->twitter->filter;

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
            'disable_web_page_preview' => true,
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
            
            $old_one = (isset($shown_tweets[$coin]) and trim($shown_tweets[$coin])) ? trim($shown_tweets[$coin]) : '';
            // $first_one = (isset($latest_tweet[0]) and trim($latest_tweet[0])) ? trim($latest_tweet[0]) : '';

            foreach ($latest_tweet as $satus_id => $text) {
                $first_one = trim($text);
                break;
            }
            // \BossBaby\Utility::writeLog('satus_id:'.serialize($satus_id));
            // \BossBaby\Utility::writeLog('text:'.serialize($text));

            if ($first_one !== $old_one) {
                // https://twitter.com/$coin
                // https://twitter.com/$username/status/1026653571377332224
                $link = "https://twitter.com/$username/status/$satus_id";
                $text = $shown_tweets[$coin] = $first_one;
                
                // Show filtered to right channels
                $filtered = false;
                
                foreach ($list_filter as $keyword) {
                    if (strpos($first_one, $keyword) !== false) {
                        $filtered = true;

                        // Format for Telegram
                        $html_links = str_replace("\n\n", "\n", $text);
                        $data['text'] = '[<a href="' . $link . '">' . $coin . '</a>] - ' . $html_links;
                        // dump($data['text']);die;
                        if (trim($data['text'])) {
                            $chat_id = $environment->telegram->channels->{1}->id;
                            $data['chat_id'] = $chat_id;

                            // \BossBaby\Utility::writeLog('text:'.serialize($data['text']));
                            $result = Longman\TelegramBot\Request::sendMessage($data);
                            // dump('$result'); dump($result);
                            // sleep(1);

                            // if ($result->isOk()) {
                            //     echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
                            // } else {
                            //     echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
                            // }
                        }

                        // Format for Discord
                        // $html_links = preg_replace('"\b(https?://\S+)"', '<a href="$1">$1</a>', $text);
                        // $html_links = preg_replace('"\b(https?://\S+)"', '<$1>', $text);
                        $html_links = str_replace("\n\n", "\n", $html_links);
                        $data['text'] = "\n\r" . PHP_EOL . '-------------------------' . PHP_EOL . '<' . $link . '>' . PHP_EOL . '```' . $html_links . '```';
                        if (trim($data['text'])) {
                            // Send message to Discord
                            $webhook_url = $environment->discord->bots->{3}->webhook_url;
                            $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
                            // \BossBaby\Utility::writeLog(__FILE__.'result2:'.serialize($result));
                            // sleep(1);
                        }

                        break;
                    }
                }

                // Show all to test channels
                if (!$filtered) {
                    // Format for Telegram
                    $html_links = str_replace("\n\n", "\n", $text);
                    $data['text'] = '[<a href="' . $link . '">' . $coin . '</a>] - ' . $html_links;
                    // dump($data['text']);die;
                    if (trim($data['text'])) {
                        // $chat_id = $environment->telegram->channels->{4}->id;
                        $chat_id = $environment->telegram->groups->{1}->id;
                        $data['chat_id'] = $chat_id;

                        // \BossBaby\Utility::writeLog('text:'.serialize($data['text']));
                        $result = Longman\TelegramBot\Request::sendMessage($data);
                        // dump('$result'); dump($result);
                        // sleep(1);

                        // if ($result->isOk()) {
                        //     echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
                        // } else {
                        //     echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
                        // }
                    }

                    // Format for Discord
                    // $html_links = preg_replace('"\b(https?://\S+)"', '<$1>', $text);
                    $html_links = str_replace("\n\n", "\n", $html_links);
                    $data['text'] = "\n\r" . PHP_EOL . '-------------------------' . PHP_EOL . '<' . $link . '>' . PHP_EOL . '```' . $html_links . '```';
                    if (trim($data['text'])) {
                        // Send message to Discord
                        $webhook_url = $environment->discord->bots->{4}->webhook_url;
                        $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
                        // \BossBaby\Utility::writeLog(__FILE__.'result2:'.serialize($result));
                        // sleep(1);
                    }
                }
            }
        }

        // Write back data into cache
        \BossBaby\Config::write($shown_tweets_file, (array) $shown_tweets);
        sleep(1);

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
