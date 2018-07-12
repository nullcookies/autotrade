<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once ("main.php");

// Detect run as CLI mode
if ($cli_mode) return require_once (ROOT_DIR . DS . 'cli-process.php');

if (!session_id()) @session_start();

// Check login
if (!isset($_SESSION['user_name']) or !$_SESSION['user_name']) {
	echo('Redirecting ...');
	func_redirect('login.php');
	exit;
}

global $options;

// ------------------------------------------------------------ //

if (count($_POST) > 0 and isset($_POST['rtype']) and $_POST['rtype'] == 'ajax' and isset($_POST['act']) and $_POST['act'] == 'put-order') {
    header('Content-Type: application/json');
    $res = array('code'=>'OK','desc'=>"DONE");
    echo json_encode($res);
    exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-current-price') {
	$arr = func_get_current_price();

	$last_orig = $arr['last'];
	$last_sess = (isset($_SESSION['getTicker']['last'])) ? $_SESSION['getTicker']['last'] : 0;
	$_SESSION['getTicker']['last'] = $last_orig;
	// $arr['sess_last'] = $last_sess;
	
	if (!isset($_SESSION['getTicker']['last'])) {
		if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '<span class="text-success">▲ ' . $arr['last'] . '</span>';
		elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '<span class="text-danger">▼ ' . $arr['last'] . '</span>';
	}
	else {
		if ($arr['last'] >= $last_sess) $arr['last'] = '<span class="text-success">▲ ' . $arr['last'] . '</span>';
		elseif ($arr['last'] < $last_sess) $arr['last'] = '<span class="text-danger">▼ ' . $arr['last'] . '</span>';
	}
	if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '<span class="text-success">▲ ' . ($arr['lastChangePcnt'] * 100) . '%</span>';
	elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '<span class="text-danger">▼ ' . ($arr['lastChangePcnt'] * 100) . '%</span>';
	else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

	func_print_arr_to_table($arr, 'Current Price');
	exit;
}

// Process load
if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-account') {
	$arr = func_get_account_info($options->account, $options->apiKey, $options->apiSecret);
	func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-account2') {
	$arr = func_get_account_info($options->account2, $options->apiKey2, $options->apiSecret2);
	func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-wallet') {
	$tmp = func_get_account_wallet($options->bitmex);
	$arr = array(
		'amount' => ($tmp['amount'] * 0.00000001) . ' BTC',
		'amount_usd' => '~' . round(($tmp['amount'] * 0.00000001) * ($_SESSION['getTicker']['last']), 3) . ' USD',
		'account' => $tmp['account'],
		'currency' => $tmp['currency'],
		'prevDeposited' => $tmp['prevDeposited'],
		'prevAmount' => $tmp['prevAmount'],
		'deposited' => $tmp['deposited'],
		// 'amount' => $tmp['amount'],
		'withdrawn' => $tmp['withdrawn'],
	);
	func_print_arr_to_table($arr);//, 'Current Wallet'
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-wallet2') {
	$tmp = func_get_account_wallet($options->bitmex2);
	$arr = array(
		'amount' => ($tmp['amount'] * 0.00000001) . ' BTC',
		'amount_usd' => '~' . round(($tmp['amount'] * 0.00000001) * ($_SESSION['getTicker']['last']), 3) . ' USD',
		'account' => $tmp['account'],
		'currency' => $tmp['currency'],
		'prevDeposited' => $tmp['prevDeposited'],
		'prevAmount' => $tmp['prevAmount'],
		'deposited' => $tmp['deposited'],
		// 'amount' => $tmp['amount'],
		'withdrawn' => $tmp['withdrawn'],
	);
	func_print_arr_to_table($arr);//, 'Current Wallet'
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-open-order') {
	$arr = $options->bitmex->getOpenOrders();
	func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-open-order2') {
	$arr = $options->bitmex2->getOpenOrders();
	func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-open-positions') {
	$arr = $options->bitmex->getOpenPositions();
	func_print_arr_to_table($arr, 'Open Positions');
	foreach ($arr as $key => $tmp) {
		func_print_arr_to_table('', $tmp);
		$arr = array(
			'openingQty' => $tmp['openingQty'],
			'leverage' => $tmp['leverage'],
			'realisedPnl' => $tmp['realisedPnl'],
			'unrealisedGrossPnl' => $tmp['unrealisedGrossPnl'],
			'unrealisedPnlPcnt' => $tmp['unrealisedPnlPcnt'],
			'unrealisedRoePcnt' => $tmp['unrealisedRoePcnt'],
			'avgCostPrice' => $tmp['avgCostPrice'],
			'avgEntryPrice' => $tmp['avgEntryPrice'],
			'breakEvenPrice' => $tmp['breakEvenPrice'],
			'marginCallPrice' => $tmp['marginCallPrice'],
			'liquidationPrice' => $tmp['liquidationPrice'],
		);
		func_print_arr_to_table($arr, '');
	}
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-actions') {
	// $options->bitmex->closePosition($price);
	// $options->bitmex->editOrderPrice($orderID, $price);

	$current = $options->bitmex->getTicker();
	// $price = $current['last'];
	$price = 6401;
	$arr = array(
		'leverage' => 5,
		'ordType' => 'Limit', // Limit | Market
		'side' => 'Buy', // Buy | Sell
		'price' => $price,
		'orderQty' => 1,
	);
	func_print_arr_to_table($arr, 'Place Order');

	// $options->bitmex->setLeverage($arr['leverage']);
	// $arr = $options->bitmex->createOrder($arr['ordType'], $arr['side'], (int) $arr['price'], (int) $arr['orderQty']);
	// func_print_arr_to_table($arr, 'Place Order');

	$arr = $options->bitmex->cancelAllOpenOrders('note to all closed orders at ' . date('H:i:s d/m/Y'));
	dump($arr); die;
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-margin') {
	$tmp = $options->bitmex->getMargin();
	$arr = array(
		'realisedPnl' => $tmp['realisedPnl'],
		'unrealisedPnl' => $tmp['unrealisedPnl'],
		'walletBalance' => $tmp['walletBalance'],
		'marginBalance' => $tmp['marginBalance'],
		'marginBalancePcnt' => $tmp['marginBalancePcnt'],
		'marginLeverage' => $tmp['marginLeverage'],
		'marginUsedPcnt' => $tmp['marginUsedPcnt'],
		'availableMargin' => $tmp['availableMargin'],
	);
	func_print_arr_to_table($arr, 'Margin');
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-orderbook') {
	$arr = $options->bitmex->getOrderBook($depth = 25);
	// func_print_arr_to_table($arr, 'OrderBook');
	if ($arr) {
		foreach ($arr as $key => $value) {
			func_print_arr_to_table($value);
		}
	}
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-orders') {
	$arr = $options->bitmex->getOrders(100);
	func_print_arr_to_table($arr, 'List User Order');
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-order') {
	$j = 0;
	for ($i=0; $i < 10; $i++) {
		$arr = $options->bitmex->getOrder($orderID = $i, $count = 100);
		if (!$arr) continue;
		$arr['$i'] = $i;
		$arr['$j'] = $j;
		if ($j>0 and ($j)%3==0) func_print_arr_to_table($arr, 'Order', array('style' => 'clear:both;'));
		else func_print_arr_to_table($arr, 'Order');
		$j++;
	}
	exit;
}
