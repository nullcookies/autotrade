<?php
/**
 * README
 * This configuration file is intended to run a list of commands with crontab.
 * Uncommented parameters must be filled
 */
$str = 'Chú ý coin thay đổi trên <b>Bittrex</b>:

<a href="https://bittrex.com/Market/Index?MarketName=BRX_BTC';
var_dump($str);die;

if (!defined('STDIN')) die('Access denied.' . "\n");

// Error handle
require_once __DIR__ . '/../error-handle.php';

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

// if (isset($_GET['test'])) {
//     // $list_coin = \BossBaby\Telegram::get_coin_pulse_binance();
//     // dump('$list_coin'); dump($list_coin);

//     $list_coin = \BossBaby\Telegram::get_coin_pulse_bittrex();
//     dump('$list_coin'); dump($list_coin);
//     die;
// }

$i = 0;
run_cron();

function run_cron() {
    global $environment;
    global $i;
    
    $i++;
    if ($i > 4) die('FINISHED');

    // Your command(s) to run, pass it just like in a message (arguments supported)
    $commands = [
        '/coinpulse'
    ];

    // Add you bot's API key and name
    $bot_api_key  = $environment->telegram->bot->{2}->token;
    $bot_username = $environment->telegram->bot->{2}->username;

    // Define all IDs of admin users in this array (leave as empty array if not used)
    $admin_users = [
        $environment->telegram->main->id,
    ];

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
        
        // Add commands paths containing your custom commands
        $telegram->addCommandsPaths($commands_paths);

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
        $telegram->setDownloadPath(LOGS_DIR);
        //$telegram->setUploadPath(__DIR__ . '/Upload');

        // Here you can set some command specific parameters,
        // e.g. Google geocode/timezone api key for /date command:
        //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

        // Botan.io integration
        //$telegram->enableBotan('your_botan_token');

        // Requests Limiter (tries to prevent reaching Telegram API limits)
        $telegram->enableLimiter();

        // Run user selected commands
        $last_command_response = $telegram->runCommands($commands);

        // dump('$last_command_response:'); dump($last_command_response);

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

    sleep(10);
    run_cron();
}
