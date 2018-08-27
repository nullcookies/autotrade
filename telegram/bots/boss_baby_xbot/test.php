<?php
/**
 * README
 * This configuration file is intended to run a list of commands with crontab.
 * Uncommented parameters must be filled
 */

// Error handle
require_once __DIR__ . '/../error-handle.php';

$text = 'set trx >= 0.00000324';
$text = 'set trx > 0.00000324';
$text = 'set trx < 0.00000324';
$text = 'set trx <= 0.00000324';
$text = 'set trx = 0.00000324';
$text = 'set trx 0.00000324';
$text = 'set trx  0.00000324';
$text = 'set  trx 0.00000324';
$text = 'set  trx  0.00000324';

$text = \BossBaby\Telegram::clean_command($text);
$text = str_ireplace('set', '', $text);
$text = \BossBaby\Telegram::clean_command($text);
dump($text);
