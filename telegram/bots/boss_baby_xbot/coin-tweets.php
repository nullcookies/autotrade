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

// Run for every 5 min
if (date('i') % 5 == 0) exit;

// Get global config
global $environment;

// Add you bot's API key and name
$bot_api_key  = $environment->telegram->bots->{2}->token;
$bot_username = $environment->telegram->bots->{2}->username;
$telegram = new Telegram($bot_api_key, $bot_username);

// File store twitter data
$twitter_config_file = CONFIG_DIR . '/twitter.php';
$twitter_config = \BossBaby\Config::read($twitter_config_file);
$twitter_config = \BossBaby\Utility::object_to_array($twitter_config);

$twitter_data = (isset($twitter_config['feeds']) and $twitter_config['feeds']) ? (array) $twitter_config['feeds'] : [];
$twitter_filter = (isset($twitter_config['filter']) and $twitter_config['filter']) ? (array) $twitter_config['filter'] : [];

if (!$twitter_data) {
    \BossBaby\Utility::writeLog(__FILE__ . '::Empty config');
    exit;
}

$shown_tweets_file = CONFIG_DIR . '/twitter_shown.php';
$shown_tweets = (is_file($shown_tweets_file) and file_exists($shown_tweets_file)) ? \BossBaby\Config::read($shown_tweets_file) : [];

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

foreach ($twitter_data as $coin => $twitter_item)
{
    $username = trim($twitter_item['username']);
    if (!$username) continue;

    $latest_tweet = \BossBaby\Telegram::get_twitter_feeds($username, 1);
    // $latest_tweet = \BossBaby\Twitter::get_twitter_feeds($username, 1);
    // \BossBaby\Utility::writeLog('latest_tweet:'.serialize($latest_tweet));

    if (!$latest_tweet) continue;
    
    $old_one = (isset($shown_tweets[$coin]) and trim($shown_tweets[$coin])) ? trim($shown_tweets[$coin]) : '';
    // $first_one = (isset($latest_tweet[0]) and trim($latest_tweet[0])) ? trim($latest_tweet[0]) : '';

    $first_one = '';
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
        
        foreach ($twitter_filter as $keyword) {
            $first_one = trim(strtolower($first_one));
            if (strpos($first_one, $keyword) !== false) {
                $filtered = true;

                // Format for Telegram
                $html_links = str_replace("\n\n", "\n", $text);
                $data['text'] = '🕊 [<a href="' . $link . '">' . $coin . '</a>] - ' . $html_links;
                // dump($data['text']);die;
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
            $data['text'] = '🕊 [<a href="' . $link . '">' . $coin . '</a>] - ' . $html_links;
            // dump($data['text']);die;
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

    sleep(1);
}

// Write back data into cache
\BossBaby\Config::write($shown_tweets_file, (array) $shown_tweets);
sleep(1);
