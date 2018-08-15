<?php
if (!defined('STDIN')) die('Access denied.' . "\n");

// chdir(__DIR__);
// defined('IS_VALID') or define('IS_VALID', 1);
// require_once __DIR__ . '/../../main.php';

// Error handle
require_once __DIR__ . '/error-handle.php';

// $result = \BossBaby\Shell::async_curl_request('/test.php');
// $result = \BossBaby\Shell::async_execute_file(__DIR__ . '/test.php');

// Check bots status
\BossBaby\Shell::async_execute_file(__DIR__ . '/status.php');

// Run cron retrive coins on Binance
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-pulse-binance.php');

// Run cron retrive coins on Binance
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-pulse-bittrex.php');

// Run cron retrive tweets on Twitter
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-tweets.php');

// Finished
die('FINISHED');
