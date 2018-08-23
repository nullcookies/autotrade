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

function run_cron() {
    // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

    // Run for every 5 min
    if (date('i') % 5 != 0) exit;
    
    // Get global config
    global $environment;

    // Add you bot's API key and name
    $bot_api_key  = $environment->telegram->bots->{2}->token;
    $bot_username = $environment->telegram->bots->{2}->username;
    $telegram = new Telegram($bot_api_key, $bot_username);

    // File store twitter data
    $twitter_config_file = CONFIG_DIR . '/twitter_config.php';
    $twitter_config = \BossBaby\Config::read($twitter_config_file);
    $twitter_config = \BossBaby\Utility::object_to_array($twitter_config);

    $twitter_data = (isset($twitter_config['feeds']) and $twitter_config['feeds']) ? (array) $twitter_config['feeds'] : [];
    $twitter_filter = (isset($twitter_config['filter']) and $twitter_config['filter']) ? (array) $twitter_config['filter'] : [];

    if (!$twitter_data) {
        \BossBaby\Utility::writeLog(__FILE__ . '::Empty config');
        exit;
    }
    
    foreach ($twitter_data as $coin => $twitter_item)
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::coin::'.serialize($coin));

        $shown_tweets_file = CONFIG_DIR . '/twitter_shown.php';
        $shown_tweets_file_tmp = $shown_tweets_file . '.lock';
        $shown_tweets = (is_file($shown_tweets_file) and file_exists($shown_tweets_file)) ? \BossBaby\Config::read_file($shown_tweets_file) : '';
        $shown_tweets = \BossBaby\Utility::object_to_array(json_decode($shown_tweets));
        if (json_last_error() or !$shown_tweets) $shown_tweets = [];

        // Add you bot's API key and name
        $bot_api_key  = $environment->telegram->bots->{2}->token;
        $bot_username = $environment->telegram->bots->{2}->username;

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

        $username = trim($twitter_item['username']);
        if (!$username) continue;

        $latest_tweet = \BossBaby\Telegram::get_twitter_feeds($username, 1);
        if (!$latest_tweet) continue;

        $old_one = (isset($shown_tweets[$coin]) and trim($shown_tweets[$coin])) ? trim($shown_tweets[$coin]) : '';
        $first_one = '';
        foreach ($latest_tweet as $satus_id => $text) {
            $first_one = trim($text);
            break;
        }

        if (clean($first_one) !== clean($old_one)) {
            // https://twitter.com/$coin
            // https://twitter.com/$username/status/1026653571377332224
            $link = "https://twitter.com/$username/status/$satus_id";
            $text = $first_one;
            
            // Show filtered to right channels
            $filtered = false;
            
            foreach ($twitter_filter as $keyword) {
                $keyword = clean($keyword);
                $first_one_tmp = clean($first_one);
                if (strpos($first_one_tmp, $keyword) !== false) {
                    $filtered = true;

                    // Format for Telegram
                    $html_links = str_replace("\n\n", "\n", $text);
                    $data['text'] = '🕊 [<a href="' . $link . '">' . $coin . '</a>] - ' . $html_links;
                    if (trim($data['text'])) {
                        $chat_id = $environment->telegram->channels->{4}->id;
                        $data['chat_id'] = $chat_id;

                        // \BossBaby\Utility::writeLog('text:'.serialize($data['text']));
                        // $result = Longman\TelegramBot\Request::sendMessage($data);
                        $result = Request::sendMessage($data);
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
                    $data['text'] = ':whale2: ' . "\n\r" . PHP_EOL . '<' . $link . '>' . PHP_EOL . '```' . $html_links . '```';
                    if (trim($data['text'])) {
                        // Send message to Discord
                        $webhook_url = $environment->discord->bots->{3}->webhook_url;
                        $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
                        // sleep(1);
                    }

                    break;
                }
            }

            // Show all to test channels
            if (!$filtered) {
                // Format for Telegram
                $html_links = str_replace("\n\n", "\n", $text);
                $data['text'] = '🕊 [<a href="' . $link . '">' . $coin . '</a>] - ' . $html_links;
                if (trim($data['text'])) {
                    $chat_id = $environment->telegram->channels->{5}->id;
                    // $chat_id = $environment->telegram->groups->{1}->id;
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
                $data['text'] = ':whale2: ' . "\n\r" . PHP_EOL . '<' . $link . '>' . PHP_EOL . '```' . $html_links . '```';
                if (trim($data['text'])) {
                    // Send message to Discord
                    $webhook_url = $environment->discord->bots->{4}->webhook_url;
                    $result = \BossBaby\Discord::sendMessage($webhook_url, $data['text']);
                    // \BossBaby\Utility::writeLog(__FILE__.'result2:'.serialize($result));
                    // sleep(1);
                }
            }
        }

        // Write back data into cache
        $shown_tweets['last_updated'] = date('Y-m-d H:i:s');
        $shown_tweets['last_updated_unix'] = time();
        $shown_tweets[$coin] = trim($first_one);

        \BossBaby\Config::write_file($shown_tweets_file_tmp, json_encode((array) $shown_tweets));
        sleep(1);
        if (is_file($shown_tweets_file_tmp) and file_exists($shown_tweets_file_tmp)) {
            @rename($shown_tweets_file_tmp, $shown_tweets_file);
            // sleep(1);
        }

        sleep(2);
    }
}

function clean($string) {
    $string = trim(strtolower($string));
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}