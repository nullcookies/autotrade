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

// Run cron retrive coins on Exchanges
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-update-bitmex.php');
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-update-binance.php');
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-update-bittrex.php');
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-update-houbipro.php');

// Run cron calculate coin pulse on Exchanges
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-pulse-binance.php');
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-pulse-bittrex.php');
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-pulse-houbipro.php');

// Run cron retrive tweets on Twitter
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-tweets.php');

// Run cron retrive coin volume Binance
\BossBaby\Shell::async_execute_file(__DIR__ . '/boss_baby_xbot/coin-vol-binance.php');

// Finished
die('FINISHED');
