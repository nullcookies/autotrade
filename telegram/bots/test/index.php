<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once("../../../main.php");

// Check config to run
if (!$environment->enable) die('STOP!!!');

// require_once('index.php');
// require_once('index2.php');
// exit;

require_once '../../vendor/autoload.php';
require_once 'stopwatch.php';

// connect to database
$mysqli = new mysqli($environment->host, $environment->user, $environment->pass, $environment->dbname);
if (!empty($mysqli->connect_errno)) {
    throw new \Exception($mysqli->connect_error, $mysqli->connect_errno);
}

// create a bot
$bot = new \TelegramBot\Api\Client($environment->token, $environment->user_name);

// run, bot, run!
$bot->run();

// /start
$bot->command('start', function ($message) use ($bot) {
    $answer = 'Howdy! Welcome to the stopwatch. Use bot commands or keyboard to control your time.';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// /go
$bot->command('go', function ($message) use ($bot, $mysqli) {
    $stopwatch = new Stopwatch($mysqli, $message->getChat()->getId());
    $stopwatch->start();
    $bot->sendMessage($message->getChat()->getId(), 'Stopwatch started. Go!');
});

// /status
$bot->command('status', function ($message) use ($bot, $mysqli) {
    $stopwatch = new Stopwatch($mysqli, $message->getChat()->getId());
    $answer = $stopwatch->status();
    if (empty($answer)) {
        $answer = 'Timer is not started.';
    }
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// stop()
$bot->command('stop', function ($message) use ($bot, $mysqli) {
    $stopwatch = new Stopwatch($mysqli, $message->getChat()->getId());
    $answer = $stopwatch->status();
    if (!empty($answer)) {
        $answer = 'Your time is ' . $answer . PHP_EOL;
    }
    $stopwatch->stop();
    $bot->sendMessage($message->getChat()->getId(), $answer . 'Stopwatch stopped. Enjoy your time!');
});

// --------------------------------------------- //
// 	// sendMessage()
// 	$keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([['/go', '/status']], null, true);

// 	$bot->sendMessage($message->getChat()->getId(), $answer, false, null, null, $keyboards);
// });
// --------------------------------------------- //

// die('boss_baby_welcome_bot!!!');