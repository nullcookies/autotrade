<?php
/**
 * README
 * This configuration file is intended to be used as the main script for the PHP Telegram Bot Manager.
 * Uncommented parameters must be filled
 *
 * For the full list of options, go to:
 * https://github.com/php-telegram-bot/telegram-bot-manager#set-extra-bot-parameters
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

try {
    $bot = new TelegramBot\TelegramBotManager\BotManager([
        // Add you bot's API key and name
        'api_key'      => $bot_api_key,
        'bot_username' => $bot_username,

        // Secret key required to access the webhook
        'secret'       => 'super_secret',

        // (array) All options that have to do with the webhook.
        'webhook'      => [
           // When using webhook, this needs to be uncommented and defined
           'url' => $environment->root_url . 'manager.php',
           // Use self-signed certificate
           // 'certificate' => __DIR__ . '/server.crt',
           // Limit maximum number of connections
           'max_connections' => 5,
        ],

        // // (bool) Only allow webhook access from valid Telegram API IPs.
        // 'validate_request' => true,
        
        // // (array) When using `validate_request`, also allow these IPs.
        // 'valid_ips'        => [
        //     '1.2.3.4',         // single
        //     '192.168.1.0/24',  // CIDR
        //     '10/8',            // CIDR (short)
        //     '5.6.*',           // wildcard
        //     '1.1.1.1-2.2.2.2', // range
        // ],

        // // (array) All options that have to do with the limiter.
        // 'limiter'          => [
        //     // (bool) Enable or disable the limiter functionality.
        //     'enabled' => true,
        //     // (array) Any extra options to pass to the limiter.
        //     'options' => [
        //         // (float) Interval between request handles.
        //         'interval' => 0.5,
        //     ],
        // ],

        // (array) All options that have to do with commands.
        'commands' => [
           // Define all paths for your custom commands
           'paths'   => [
               __DIR__ . '/Commands',
           ],
           // Here you can set some command specific parameters
           // 'configs' => [
           //     // e.g. Google geocode/timezone api key for /date command
           //     'date' => ['google_api_key' => 'your_google_api_key_here'],
           // ],
        ],

        // Define all IDs of admin users
        //'admins'       => [
        //    123,
        //],

        // Enter your MySQL database credentials
        // // (array) Mysql credentials to connect a database (necessary for [`getUpdates`](#using-getupdates-method) method!).
        'mysql'            => [
            'host'         => $environment->host,
            'port'         => 3306,           // optional
            'user'         => $environment->user,
            'password'     => $environment->pass,
            'database'     => $environment->dbname,
            // 'table_prefix' => 'tbl_',    // optional
            'encoding'     => 'utf8mb4',      // optional
        ],

        // Logging (Error, Debug and Raw Updates)
        'logging'  => [
           'debug'  => LOGS_DIR . "/{$bot_username}_debug.log",
           'error'  => LOGS_DIR . "/{$bot_username}_error.log",
           'update' => LOGS_DIR . "/{$bot_username}_update.log",
        ],

        // Set custom Upload and Download paths
        //'paths'    => [
        //    'download' => __DIR__ . '/Download',
        //    'upload'   => __DIR__ . '/Upload',
        //],

        // Botan.io integration
        //'botan' => [
        //    'token' => 'your_botan_token',
        //],

        // (array) All options that have to do with cron.
        // 'cron'             => [
        //     // (array) List of groups that contain the commands to execute.
        //     'groups' => [
        //         // Each group has a name and array of commands.
        //         // When no group is defined, the default group gets executed.
        //         'default'     => [
        //             '/default_cron_command',
        //         ],
        //         'maintenance' => [
        //             '/db_cleanup',
        //             '/db_repair',
        //             '/log_rotate',
        //             '/message_admins Maintenance completed',
        //         ],
        //     ],
        // ],

        // (string) Override the custom input of your bot (mostly for testing purposes!).
        // 'custom_input'     => '{"some":"raw", "json":"update"}',

        // Requests Limiter (tries to prevent reaching Telegram API limits)
        'limiter'      => ['enabled' => true],
    ]);

    // Run the bot!
    $bot->run();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // Default error
    // echo $e;
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Silence is golden!
    // Uncomment this to catch log initialisation errors
    // echo $e;
}
