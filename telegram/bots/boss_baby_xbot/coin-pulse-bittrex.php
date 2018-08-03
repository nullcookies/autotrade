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

$sleep = 9;

// $i = 0;
// run_cron();

run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();

function run_cron() {
    // dump(__FUNCTION__ . '::' . time());
    // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

    global $environment;
    global $count_run_cron_bittrex;
    
    // $i++;
    // if ($i > 4) die('FINISHED');

    // // Your command(s) to run, pass it just like in a message (arguments supported)
    // $commands = [
    //     '/coinpulse'
    // ];

    // Add you bot's API key and name
    $bot_api_key  = $environment->telegram->bot->{2}->token;
    $bot_username = $environment->telegram->bot->{2}->username;

    // Define all IDs of admin users in this array (leave as empty array if not used)
    // $admin_users = [
    //     $environment->telegram->main->id,
    // ];

    // Define all paths for your custom commands in this array (leave as empty array if not used)
    $commands_paths = [
       __DIR__ . '/Commands/',
    ];

    // // Enter your MySQL database credentials
    // $mysql_credentials = [
    //     'host'     => $environment->database->{1}->host,
    //     'user'     => $environment->database->{1}->user,
    //     'password' => $environment->database->{1}->pass,
    //     'database' => $environment->database->{1}->name,
    // ];

    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
        
        // // Add commands paths containing your custom commands
        // $telegram->addCommandsPaths($commands_paths);

        // // Enable admin users
        // $telegram->enableAdmins($admin_users);

        // Enable MySQL
        //$telegram->enableMySql($mysql_credentials);

        // Logging (Error, Debug and Raw Updates)
        Longman\TelegramBot\TelegramLog::initErrorLog(LOGS_DIR . "/{$bot_username}_error.log");
        // Longman\TelegramBot\TelegramLog::initDebugLog(LOGS_DIR . "/{$bot_username}_debug.log");
        Longman\TelegramBot\TelegramLog::initUpdateLog(LOGS_DIR . "/{$bot_username}_update.log");

        // If you are using a custom Monolog instance for logging, use this instead of the above
        //Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);

        // Set custom Upload and Download paths
        //$telegram->setDownloadPath(LOGS_DIR);
        //$telegram->setUploadPath(__DIR__ . '/Upload');

        // Here you can set some command specific parameters,
        // e.g. Google geocode/timezone api key for /date command:
        //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

        // Botan.io integration
        //$telegram->enableBotan('your_botan_token');

        // Requests Limiter (tries to prevent reaching Telegram API limits)
        // $telegram->enableLimiter();

        // Run user selected commands
        // $last_command_response = $telegram->runCommands($commands);

        // dump('$last_command_response:'); dump($last_command_response);

        // $chat_id   = $message->getChat()->getId();
        $chat_id   = $environment->telegram->channel->{3}->id;
        // $chat_id   = $environment->telegram->main->id;

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'html',
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

        if ($list_coin_bittrex) {
            foreach ($list_coin_bittrex as $text) {
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

//     sleep(10);
//     run_cron();
}
