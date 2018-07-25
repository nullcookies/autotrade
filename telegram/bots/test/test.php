<?php
chdir(__DIR__);
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

$botToken = $environment->token;
$apiURL = "https://api.telegram.org/bot" . $botToken;

// $update = file_get_contents($apiURL . '/getupdates');
$update = file_get_contents('php://input');
// dump($update);

$updates = json_decode($update, true);
// dump($updates);

$chatId = $updates['result'][0]['message']['chat']['id'];
// dump($chatId);

file_get_contents($apiURL . '/sendmessage?chat_id=' . $chatId . '&text=test-' . time());