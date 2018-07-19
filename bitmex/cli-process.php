<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once("../main.php");
require_once(dirname(__FILE__) . DS . "function.php");
require_once(LIB_DIR . DS ."bitmex-api/BitMex.php");

// Detect run as CLI mode
if (!$cli_mode) return \Utility::func_redirect('index.php');

// Get global variables
global $environment;
$environment = new stdClass();

$config_file = dirname(__FILE__) . DS . "config.php";
$config = \Utility::func_read_config($config_file);
if (is_array($config) and count($config)) {
	foreach ($config as $key => $value) {
		$environment->$key = $value;
	}
	// if ($environment->apiKey and $environment->apiSecret)
	// 	$environment->bitmex = new BitMex($environment->apiKey, $environment->apiSecret);
	// if ($environment->apiKey2 and $environment->apiSecret2)
	// 	$environment->bitmex2 = new BitMex($environment->apiKey2, $environment->apiSecret2);
}

// ------------------------------------------------------------ //

global $_check_price;
$_check_price = 0;
function func_show_current_price()
{
	global $environment;
	global $_check_price;
	$_check_price++;

	if (date('i') == '03') $environment->can_run = false;
	
	if ($environment->can_run) {
		// echo date('Y-m-d H:i:s') . ' -> ' . $environment->can_run . "\n";
		if ($_check_price > 1) echo "\n";
		echo 'Time: ' . date('Y-m-d H:i:s') . ' -> ' . $_check_price . "\n";

		$arr = func_get_current_price();

		$last_orig = $arr['last'];
		$last_sess = (isset($_SESSION['getTicker']['last'])) ? $_SESSION['getTicker']['last'] : 0;
		$_SESSION['getTicker']['last'] = $last_orig;
		// $arr['sess_last'] = $last_sess;
		
		if (!isset($_SESSION['getTicker']['last'])) {
			if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '▲ ' . $arr['last'];
			elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '▼ ' . $arr['last'];
		}
		else {
			if ($arr['last'] >= $last_sess) $arr['last'] = '▲ ' . $arr['last'];
			elseif ($arr['last'] < $last_sess) $arr['last'] = '▼ ' . $arr['last'];
		}
		if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '▲ ' . ($arr['lastChangePcnt'] * 100) . '%';
		elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '▼ ' . ($arr['lastChangePcnt'] * 100) . '%';
		else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

		\Utility::func_cli_print_arr($arr);

		func_show_account_info();

		sleep(rand(10,15));
		func_show_current_price();
	}

	else {
		die('STOP!!!' . "\n");
	}
}
func_show_current_price();

function func_show_account_info()
{
	global $environment;
	
	$arr1 = func_get_account_info($environment->account, $environment->apiKey, $environment->apiSecret, false);
	\Utility::func_cli_print_arr($arr1);

	$arr2 = func_get_account_info($environment->account2, $environment->apiKey2, $environment->apiSecret2, false);
	\Utility::func_cli_print_arr($arr2);
}

function func_show_account_wallet()
{
	global $environment;
	
	if (is_null($environment->bitmex)) $environment->bitmex = new BitMex($environment->apiKey, $environment->apiSecret);
	$arr1 = func_get_account_wallet($environment->bitmex);
	\Utility::func_cli_print_arr($arr1);

	if (is_null($environment->bitmex)) $environment->bitmex2 = new BitMex($environment->apiKey2, $environment->apiSecret2);
	$arr2 = func_get_account_wallet($environment->bitmex2);
	\Utility::func_cli_print_arr($arr2);
}

