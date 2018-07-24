#!/usr/bin/php
<?php
/**
 * README
 * This configuration file is intended to run the bot with the getUpdates method.
 * Uncommented parameters must be filled
 *
 * Bash script:
 * $ while true; do ./getUpdatesCLI.php; done
 */

defined('IS_VALID') or define('IS_VALID', 1);
require_once("../../../main.php");

// Get global variables
$environment = new stdClass();

$config_file = dirname(__FILE__) . DS . "config.php";
$config = \Utility::func_read_config($config_file);
if (is_array($config) and count($config)) {
    foreach ($config as $key => $value) {
        $environment->$key = $value;
    }
}

// Check config to run
if (!$environment->can_run) die('STOP!!!');

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

// Add you bot's API key and name
$bot_api_key  = $environment->token;
$bot_username = $environment->user_name;

// Define all IDs of admin users in this array (leave as empty array if not used)
$admin_users = [
//    123,
];

// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
    __DIR__ . '/Commands/',
];

// Enter your MySQL database credentials
// $mysql_credentials = [
//     'host'     => $environment->host,
//     'user'     => $environment->user,
//     'password' => $environment->pass,
//     'database' => $environment->dbname,
// ];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Add commands paths containing your custom commands
    $telegram->addCommandsPaths($commands_paths);

    // Enable admin users
    $telegram->enableAdmins($admin_users);

    // Enable MySQL
    $telegram->enableMySql($mysql_credentials);

    // Logging (Error, Debug and Raw Updates)
    Longman\TelegramBot\TelegramLog::initErrorLog(LOGS_DIR . "/{$bot_username}_error.log");
    // Longman\TelegramBot\TelegramLog::initDebugLog(LOGS_DIR . "/{$bot_username}_debug.log");
    Longman\TelegramBot\TelegramLog::initUpdateLog(LOGS_DIR . "/{$bot_username}_update.log");

    // If you are using a custom Monolog instance for logging, use this instead of the above
    //Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);

    // Set custom Upload and Download paths
    $telegram->setDownloadPath(LOGS_DIR);
    //$telegram->setUploadPath(__DIR__ . '/Upload');

    // Here you can set some command specific parameters
    // e.g. Google geocode/timezone api key for /date command
    //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

    // Botan.io integration
    //$telegram->enableBotan('your_botan_token');

    // Requests Limiter (tries to prevent reaching Telegram API limits)
    $telegram->enableLimiter();

    // Handle telegram getUpdates request
    $server_response = $telegram->handleGetUpdates();

    if ($server_response->isOk()) {
        $update_count = count($server_response->getResult());
        echo date('Y-m-d H:i:s', time()) . ' - Processed ' . $update_count . ' updates';
    } else {
        echo date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . PHP_EOL;
        echo $server_response->printError();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Catch log initialisation errors
    echo $e->getMessage();
}
