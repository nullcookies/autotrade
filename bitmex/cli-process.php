<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once ("main.php");

// Detect run as CLI mode
if (!$cli_mode) return redirect('index.php');

// ============================================================ //

require_once ("library/bitmex-api/BitMex.php");

$account = 'signvltk1@gmail.com';
$apiKey = 'P5RaBUJ-8NZsxG_E5x5p6C_B';
$apiSecret = 'FZ-zqEpiqVPlHOtBu4rMbwx26ZeRoZbQ-RzSiyGv6E9c9epy';
$bitmex = new BitMex($apiKey, $apiSecret);

$account2 = 'long.vu0104@gmail.com';
$apiKey2 = 'q1KYRfGHroeROIjRvdsvhJqv';
$apiSecret2 = 'iCiuNYv_F4rdZkkc2R89bzMLb5KkkINkIkXHpEnN8sp1DEi3';
$bitmex2 = new BitMex($apiKey2, $apiSecret2);

// if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-current-price') {
	$arr = $bitmex->getTicker();

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

	dump($arr);
	exit;
// }

if (count($_POST) > 0 and isset($_POST['rtype']) and $_POST['rtype'] == 'ajax' and isset($_POST['act']) and $_POST['act'] == 'put-order') {
    header('Content-Type: application/json');
    $res = array('code'=>'OK','desc'=>"DONE");
    echo json_encode($res);
    exit;
}

// Process load
if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-account') {
	$arr = array(
		'Account' => $account,
		'API Key' => $apiKey,
		'API Secret' => replace_by_star($apiSecret),
	);
	print_arr1_to_table($arr);
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-account2') {
	$arr = array(
		'Account' => $account2,
		'API Key' => $apiKey2,
		'API Secret' => replace_by_star($apiSecret2),
	);
	print_arr1_to_table($arr);
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-wallet') {
	$tmp = $bitmex->getWallet();
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
	print_arr1_to_table($arr);//, 'Current Wallet'
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-wallet2') {
	$tmp = $bitmex2->getWallet();
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
	print_arr1_to_table($arr);//, 'Current Wallet'
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-list-order') {
	$arr = $bitmex->getOpenOrders();
	print_arr1_to_table($arr, 'List Open Order');
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-open-positions') {
	$arr = $bitmex->getOpenPositions();
	print_arr1_to_table($arr, 'Open Positions');
	foreach ($arr as $key => $tmp) {
		print_arr1_to_table('', $tmp);
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
		print_arr1_to_table($arr, '');
	}
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-actions') {
	// $bitmex->closePosition($price);
	// $bitmex->editOrderPrice($orderID, $price);

	$current = $bitmex->getTicker();
	// $price = $current['last'];
	$price = 6401;
	$arr = array(
		'leverage' => 5,
		'ordType' => 'Limit', // Limit | Market
		'side' => 'Buy', // Buy | Sell
		'price' => $price,
		'orderQty' => 1,
	);
	print_arr1_to_table($arr, 'Place Order');

	// $bitmex->setLeverage($arr['leverage']);
	// $arr = $bitmex->createOrder($arr['ordType'], $arr['side'], (int) $arr['price'], (int) $arr['orderQty']);
	// print_arr1_to_table($arr, 'Place Order');

	$arr = $bitmex->cancelAllOpenOrders('note to all closed orders at ' . date('H:i:s d/m/Y'));
	dump($arr); die;
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-margin') {
	$tmp = $bitmex->getMargin();
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
	print_arr1_to_table($arr, 'Margin');
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-orderbook') {
	$arr = $bitmex->getOrderBook($depth = 25);
	// print_arr1_to_table($arr, 'OrderBook');
	if ($arr) {
		foreach ($arr as $key => $value) {
			print_arr1_to_table($value);
		}
	}
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-orders') {
	$arr = $bitmex->getOrders(100);
	print_arr1_to_table($arr, 'List User Order');
	exit;
}

if (count($_GET) > 0 and isset($_GET['rtype']) and $_GET['rtype'] == 'ajax' and isset($_GET['act']) and $_GET['act'] == 'load-order') {
	$j = 0;
	for ($i=0; $i < 10; $i++) {
		$arr = $bitmex->getOrder($orderID = $i, $count = 100);
		if (!$arr) continue;
		$arr['$i'] = $i;
		$arr['$j'] = $j;
		if ($j>0 and ($j)%3==0) print_arr1_to_table($arr, 'Order', array('style' => 'clear:both;'));
		else print_arr1_to_table($arr, 'Order');
		$j++;
	}
	exit;
}
