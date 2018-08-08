<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once __DIR__ . '/../main.php';

// Detect run as CLI mode
if (!$cli_mode) return \BossBaby\Utility::redirect('index.php');

// Check config to run
if (!$environment->enable) die('STOP!!!');

$environment->bitmex_instance = new \Bitmex($environment->bitmex->accounts->{1}->apiKey, $environment->bitmex->accounts->{1}->apiSecret);
// $environment->bitmex_instance2 = new \Bitmex($environment->bitmex->accounts->{2}->apiKey, $environment->bitmex->accounts->{2}->apiSecret);
$environment->bitmex_instance2 = $environment->bitmex_instance;
$environment->bitmex_instance3 = new \Bitmex($environment->bitmex->accounts->{3}->apiKey, $environment->bitmex->accounts->{3}->apiSecret);

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
	
	global $_current_price;
	global $_check_price;
	$_check_price++;

	if ($environment->enable) {
		// echo date('Y-m-d H:i:s') . ' -> ' . $environment->enable . "\n";
		// if ($_check_price > 1) 
		// echo "\n";
		// echo 'Time: ' . date('Y-m-d H:i:s') . ' -> ' . $_check_price . "\n";

		$arr = \BossBaby\Bitmex::func_get_current_price($environment->bitmex_instance);

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

	$arr1 = \BossBaby\Bitmex::func_get_account_info($environment->bitmex->accounts->{2}->email, $environment->bitmex->accounts->{2}->apiKey, $environment->bitmex->accounts->{2}->apiSecret, false);
	\BossBaby\Utility::func_cli_print_arr($arr1);

	$arr2 = \BossBaby\Bitmex::func_get_account_info($environment->bitmex->accounts->{3}->email, $environment->bitmex->accounts->{3}->apiKey, $environment->bitmex->accounts->{3}->apiSecret, false);
	\BossBaby\Utility::func_cli_print_arr($arr2);
}

function func_show_account_wallet()
{
	global $environment;

	$arr1 = \BossBaby\Bitmex::func_get_account_wallet($environment->bitmex_instance2);
	\BossBaby\Utility::func_cli_print_arr($arr1);

	$arr2 = \BossBaby\Bitmex::func_get_account_wallet($environment->bitmex_instance3);
	\BossBaby\Utility::func_cli_print_arr($arr2);
}

