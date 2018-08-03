<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once("../../../main.php");

// Check config to run
if (!$environment->enable) die('STOP!!!');

$botToken = $environment->token;
$apiURL = "https://api.telegram.org/bot" . $botToken;

// $update = file_get_contents($apiURL . '/getupdates');
$update = file_get_contents('php://input');
// dump($update);

$updates = json_decode($update, true);
// dump($updates);

// $chatId = $updates['result'][0]['message']['chat']['id'];
$chatId = $updates['message']['chat']['id'];
$message = $updates['message']['text'];
// dump($chatId);

switch ($message) {
	case '/test':
		sendMessage($chatId, 'test ' . date('H:i:s m/d/Y'));
		break;
	case '/hi':
		sendMessage($chatId, 'hey there ' . date('H:i:s m/d/Y'));
		break;
	default:
		sendMessage($chatId, 'default ' . date('H:i:s m/d/Y'));
		break;
}

function sendMessage($chatId, $message) {
	$url = $GLOBALS[apiURL] . '/sendMessage?chat_id=' . $chatId . '&text=' . urlencode($message);
	file_get_contents($url);
}
