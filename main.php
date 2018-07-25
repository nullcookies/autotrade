<?php
if (!defined('IS_VALID')) die('Access denied.' . "\n");

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('ROOT_DIR') or define('ROOT_DIR', dirname(__FILE__));
defined('LIB_DIR') or define('LIB_DIR', ROOT_DIR . DS . 'library');
defined('LOGS_DIR') or define('LOGS_DIR', ROOT_DIR . DS . 'logs');
// defined('CONFIG_FILE') or define('CONFIG_FILE', ROOT_DIR . DS . 'config.php');

set_include_path(get_include_path() . PATH_SEPARATOR . LIB_DIR);

// require_once(LIB_DIR . DS . "bossbaby/utility.php");
// require_once(LIB_DIR . DS . "bossbaby/shell.php");

// require_once LIB_DIR . '/bossbaby/autoload.php';
require_once(LIB_DIR . "/bossbaby/Autoloader.php");

new \BossBaby\Config;
// var_dump($config);
die;

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

// Detect run as CLI mode
// $isCLI = ( php_sapi_name() == 'cli' );
$cli_mode = (php_sapi_name() == "cli") ? true : false;
defined('CLI_MODE') or define('CLI_MODE', $cli_mode);

/* AJAX check  */
$ajax_mode = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
defined('AJAX_MODE') or define('AJAX_MODE', $ajax_mode);

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Report all errors except E_NOTICE and E_WARNING
if (!is_dir(LOGS_DIR)) mkdir(LOGS_DIR, 0777);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set("log_errors", 1);
ini_set("error_log", LOGS_DIR . DS . date("Ymd") . "-log.txt");

$is_https = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
    || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
$root_url = '';
$http_host = $_SERVER['HTTP_HOST'];
$root_url = \Utility::func_clean_path($root_url);
defined('ROOT_URL') or define('ROOT_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . (!empty($root_url) ? '/' . $root_url : ''));
defined('SELF_URL') or define('SELF_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . $_SERVER['PHP_SELF']);
defined('SELF_URL_NO_SCRIPT') or define('SELF_URL_NO_SCRIPT', dirname(strtok(SELF_URL, '?')) . '/');
