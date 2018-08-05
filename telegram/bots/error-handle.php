<?php
// if (!defined('STDIN')) die('Access denied.' . "\n");

// if (!isset($_GET['secret']) || $_GET['secret'] !== 'AihezooSahc0aiquu3aigai2Phee2ien') {
//     die('Access denied.' . "\n");
// }

// // Set the lower and upper limit of valid Telegram IPs.
// // https://core.telegram.org/bots/webhooks#the-short-version
// $telegram_ip_lower = '149.154.167.197';
// $telegram_ip_upper = '149.154.167.233';

// // Get the real IP.
// $ip = $_SERVER['REMOTE_ADDR'];
// foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR'] as $key) {
//     $addr = @$_SERVER[$key];
//     if (filter_var($addr, FILTER_VALIDATE_IP)) {
//         $ip = $addr;
//     }
// }

// // Make sure the IP is valid.
// $lower_dec = (float) sprintf("%u", ip2long($telegram_ip_lower));
// $upper_dec = (float) sprintf("%u", ip2long($telegram_ip_upper));
// $ip_dec    = (float) sprintf("%u", ip2long($ip));
// if ($ip_dec < $lower_dec || $ip_dec > $upper_dec) {
//     die("Hmm, I don't trust you...");
// }

chdir(__DIR__);
defined('IS_VALID') or define('IS_VALID', 1);
require_once __DIR__ . '/../../main.php';

// Check config to run
if (!$environment->enable) die('STOP!!!');

/**
 * Error handler, passes flow over the exception logger with new ErrorException.
 *
 * @param $num
 * @param $str
 * @param $file
 * @param $line
 * @param null $context
 */
if (!function_exists('log_error')) {
    function log_error($num, $str, $file, $line, $context = null)
    {
        log_exception(new ErrorException($str, 0, $num, $file, $line));
    }
}

/**
 * Uncaught exception handler.
 * @param \Exception $e
 */
if (!function_exists('log_exception')) {
    function log_exception($e)
    {
        global $environment;
        
        // setup notifier
        $API_KEY = $environment->telegram->bots->{1}->token; // Replace 'XXXXXXXXXX' with your bot's API token
        $DEV_ID  = $environment->telegram->main->id; // Replace 'XXXXXXXXXX' with your Telegram user ID (use /whoami command)

        // get incomming message
        $incoming = file_get_contents('php://input');
        
        // if message exist convert it into array
        $incoming = !empty($incoming) ? json_decode(file_get_contents('php://input'), true) : false;
        
        // developer notification message text
        $file = str_replace('/home/dosuser02/websites/testing', '/fake/path', $e->getFile());
        $file = str_replace('/home/dosuser02', '/fake/path', $file);
        $trace = str_replace('/home/dosuser02/websites/testing', '/fake/path', $e->getTraceAsString());
        $trace = str_replace('/home/dosuser02', '/fake/path', $trace);
        
        $message = get_class($e);
        $message .= PHP_EOL . "Message: <b>{$e->getMessage()}</b>";
        $message .= PHP_EOL . "File: <b>" . $file . "</b>";
        $message .= PHP_EOL . "Line: <b>{$e->getLine()}</b>";
        $message .= PHP_EOL . "Time: <b>" . date("H:i:s / d.m.Y") . "</b>";
        $message .= PHP_EOL . ((!empty($incoming)) ? "<b>Incoming message:</b><pre>" . PHP_EOL . var_export($incoming, true) . '</pre>' : PHP_EOL . '<b>Trace:</b><pre>' . $trace . '</pre>');
        
        // developer notification message settings
        $fields_string = '';
        $url           = 'https://api.telegram.org/bot' . $API_KEY . '/sendMessage';
        
        $fields = [
            'chat_id' => urlencode($DEV_ID),
            'parse_mode' => urlencode('HTML'),
            'text' => urlencode('' . $message)
        ];
        
        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        //execute post
        $result = curl_exec($ch);
        
        //close connection
        curl_close($ch);
        
        // Uncomment following line and change path to store errors log in custom file
        // file_put_contents( __DIR__ .'/custom_errors.log', ($result?'Notified: '.var_export($result, true).PHP_EOL:'Not notified: '.var_export($result, true).PHP_EOL).$message . PHP_EOL, FILE_APPEND );
        
        // Sending 200 response code
        header('X-PHP-Response-Code: 200', true, 200);
        
        exit();
    }
}

/**
 * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
 */
if (!function_exists('check_for_fatal')) {
    function check_for_fatal()
    {
        $error = error_get_last();
        if ($error["type"] == E_ERROR)
            log_error($error["type"], $error["message"], $error["file"], $error["line"]);
    }
}

register_shutdown_function("check_for_fatal");
set_error_handler("log_error");
set_exception_handler("log_exception");
ini_set("display_errors", "off");
error_reporting(E_ALL);
