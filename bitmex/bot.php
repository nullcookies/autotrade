<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once("../main.php");
require_once(LIB_DIR . DS . "bitmex-api/BitMex.php");
require_once("function.php");

// Detect run as CLI mode
if (!$cli_mode) return \BossBaby\Utility::redirect('index.php');

$botToken = $environment->token;
$apiURL = "https://api.telegram.org/bot" . $botToken;

$update = file_get_contents($apiURL . '/getupdates');
if (!$update)
	$update = file_get_contents('php://input');
$updates = json_decode($update, true);
// dump($update);
// dump($updates);

$chatId = $updates['result'][0]['message']['chat']['id'];
if (!$chatId)
	$chatId = $updates['message']['chat']['id'];
// dump($chatId);

$_current_price = 0;
$_check_price = 0;
func_show_current_price();
exit;

$message = $updates['message']['text'];
switch ($message) {
	case '/start':
		sendMessage($chatId, 'start ' . date('H:i:s m/d/Y'));
		// $_current_price = 0;
		// $_check_price = 0;
		// func_show_current_price();
		break;
	default:
		sendMessage($chatId, 'default ' . date('H:i:s m/d/Y'));
		break;
}

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

	if ($environment->enable) {
		// echo date('Y-m-d H:i:s') . ' -> ' . $environment->enable . "\n";
		// if ($_check_price > 1) 
		// echo "\n";
		// echo 'Time: ' . date('Y-m-d H:i:s') . ' -> ' . $_check_price . "\n";

		$arr = func_get_current_price();

		$last_orig = $arr['last'];
		$last_sess = (isset($_current_price)) ? $_current_price : 0;
		$_current_price = $last_orig;
		// $arr['sess_last'] = $last_sess;
		
		if (!isset($_current_price)) {
			if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '⬆️ ' . $arr['last'];
			elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '⏬ ' . $arr['last'];
		}
		else {
			if ($arr['last'] >= $last_sess) $arr['last'] = '⬆️ ' . $arr['last'];
			elseif ($arr['last'] < $last_sess) $arr['last'] = '⏬ ' . $arr['last'];
		}
		if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '⬆️ ' . ($arr['lastChangePcnt'] * 100) . '%';
		elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '⏬ ' . ($arr['lastChangePcnt'] * 100) . '%';
		else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

		global $chatId;
		sendMessage($chatId, func_telegram_print_arr($arr));

		// func_show_account_info();

		// sleep(15);
		// func_show_current_price();
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
            $text .= $key . ': ' . serialize($value) . "\n";
        }
        else {
            $text .= $key . ': ' . $value . "\n";
        }
    }
    $text .= "\n";

    return $text;
}

function func_show_account_info()
{
	global $environment;
	
	$arr1 = func_get_account_info($environment->account, $environment->bitmex->{2}->apiKey, $environment->bitmex->{2}->apiSecret, false);
	\BossBaby\Utility::func_cli_print_arr($arr1);

	$arr2 = func_get_account_info($environment->account2, $environment->bitmex->{2}->apiKey2, $environment->bitmex->{2}->apiSecret2, false);
	\BossBaby\Utility::func_cli_print_arr($arr2);
}

function func_show_account_wallet()
{
	global $environment;
	
	if (property_exists('stdClass', 'bitmex') === false or is_null($environment->bitmex))
		$environment->bitmex_instance = new BitMex($environment->bitmex->{2}->apiKey, $environment->bitmex->{2}->apiSecret);

	$arr1 = func_get_account_wallet($environment->bitmex);
	\BossBaby\Utility::func_cli_print_arr($arr1);

	if (property_exists('stdClass', 'bitmex2') === false or is_null($environment->bitmex2))
		$environment->bitmex2_instance = new BitMex($environment->bitmex->{3}->apiKey2, $environment->bitmex->{3}->apiSecret2);

	$arr2 = func_get_account_wallet($environment->bitmex2);
	\BossBaby\Utility::func_cli_print_arr($arr2);
}

