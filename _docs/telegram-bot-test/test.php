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

$chatId = $updates['result'][0]['message']['chat']['id'];
// dump($chatId);

file_get_contents($apiURL . '/sendmessage?chat_id=' . $chatId . '&text=test-' . time());