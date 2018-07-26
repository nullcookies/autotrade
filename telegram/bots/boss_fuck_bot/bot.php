<?php
/**
 * Usage on CLI: $ php broadcast.php [telegram-chat-id] [message]
 */

// Error handle
require_once(__DIR__ . "/../error-handle.php");

require_once(LIB_DIR . DS . "bitmex-api/BitMex.php");
require_once(ROOT_DIR . DS . "bitmex/function.php");

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

$API_KEY  = $environment->telegram->bot->{3}->token;
$BOT_NAME = $environment->telegram->bot->{3}->username;

$telegram = new Telegram($API_KEY, $BOT_NAME);

// Get the chat id and message text from the CLI parameters.
$chat_id = isset($argv[1]) ? $argv[1] : $environment->telegram->id;
$message = isset($argv[2]) ? $argv[2] : 'Message at ' . date('H:i:s d/m/Y');

$_current_price = 0;
$_check_price = 0;
func_show_current_price();

// ------------------------------------------------------------ //

function sendMessage($chatId, $message) {
	$url = $GLOBALS[apiURL] . '/sendMessage?chat_id=' . $chatId . '&text=' . urlencode($message);
	file_get_contents($url);
}

function func_bind_current_config()
{
	// ☺☻♥♦♣♠•◘○◙♂♀♪♫☼►◄↕‼¶§▬↨↑↓→←
	global $environment;
}

function func_show_current_price()
{
	// Get current config
	global $environment;
	// func_bind_current_config();
	
	global $_current_price;
	global $_check_price;
	$_check_price++;

	if ($environment->telegram->bot->{3}->enable) {
		// echo date('Y-m-d H:i:s') . ' -> ' . $environment->telegram->bot->{3}->enable . "\n";
		// if ($_check_price > 1) 
		// echo "\n";
		// echo 'Time: ' . date('Y-m-d H:i:s') . ' -> ' . $_check_price . "\n";

		$arr = func_get_current_price();

		$last_orig = $arr['last'];
		$last_sess = (isset($_current_price)) ? $_current_price : 0;
		$_current_price = $last_orig;
		// $arr['sess_last'] = $last_sess;
		
		if (!isset($_current_price)) {
			if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '👆 ' . $arr['last'];
			elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '👇 ' . $arr['last'];
		}
		else {
			if ($arr['last'] >= $last_sess) $arr['last'] = '👆 ' . $arr['last'];
			elseif ($arr['last'] < $last_sess) $arr['last'] = '👇 ' . $arr['last'];
		}
		if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '👆 ' . ($arr['lastChangePcnt'] * 100) . '%';
		elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '👇 ' . ($arr['lastChangePcnt'] * 100) . '%';
		else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

		$arr['Changed'] = $arr['lastChangePcnt']; unset($arr['lastChangePcnt']);

		// global $chatId;
		// sendMessage($chatId, func_telegram_print_arr($arr));

		global $chat_id;
		$message = func_telegram_print_arr($arr);

		$message = str_replace('Symbol:', '', $message);

		if ($chat_id !== '' && $message !== '') {
			$data = [
			    'chat_id' => $chat_id,
			    'text'    => $message,
			    'parse_mode' => urlencode('HTML'),
			];

			$result = Request::sendMessage($data);

			// if ($result->isOk()) {
			//     echo 'Message sent succesfully to: ' . $chat_id;
			// } else {
			//     echo 'Sorry message not sent to: ' . $chat_id;
			// }
		}

		// func_show_account_info();

		sleep(30);
		func_show_current_price();
	}

	else {
		die('STOP!!!' . "\n");
	}
}

function func_telegram_print_arr($arr = null) 
{
    if (!$arr) return 'No data found!';
    $text = "\n";
    foreach ($arr as $key => $value) {
        if (is_object($value)) {
            $text .= ucfirst($key) . ': ' . serialize($value) . "\n";
        }
        else {
            $text .= ucfirst($key) . ': ' . $value . "\n";
        }
    }
    $text .= "\n";

    return $text;
}

function func_show_account_info()
{
	global $environment;
	
	$arr1 = func_get_account_info($environment->telegram->bot->{3}->account, $environment->telegram->bot->{3}->bitmex->{2}->apiKey, $environment->telegram->bot->{3}->bitmex->{2}->apiSecret, false);
	\BossBaby\Utility::func_cli_print_arr($arr1);

	$arr2 = func_get_account_info($environment->telegram->bot->{3}->account2, $environment->telegram->bot->{3}->bitmex->{2}->apiKey2, $environment->telegram->bot->{3}->bitmex->{2}->apiSecret2, false);
	\BossBaby\Utility::func_cli_print_arr($arr2);
}

function func_show_account_wallet()
{
	global $environment;
	
	if (property_exists('stdClass', 'bitmex') === false or is_null($environment->telegram->bot->{3}->bitmex))
		$environment->telegram->bot->{3}->bitmex = new BitMex($environment->telegram->bot->{3}->bitmex->{2}->apiKey, $environment->telegram->bot->{3}->bitmex->{2}->apiSecret);

	$arr1 = func_get_account_wallet($environment->telegram->bot->{3}->bitmex);
	\BossBaby\Utility::func_cli_print_arr($arr1);

	if (property_exists('stdClass', 'bitmex2') === false or is_null($environment->telegram->bot->{3}->bitmex2))
		$environment->telegram->bot->{3}->bitmex2 = new BitMex($environment->telegram->bot->{3}->bitmex->{3}->apiKey2, $environment->telegram->bot->{3}->bitmex->{3}->apiSecret2);
	
	$arr2 = func_get_account_wallet($environment->telegram->bot->{3}->bitmex2);
	\BossBaby\Utility::func_cli_print_arr($arr2);
}

