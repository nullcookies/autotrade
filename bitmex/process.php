<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once("../main.php");
require_once(LIB_DIR . DS . "bitmex-api/BitMex.php");

// Detect run as CLI mode
if ($cli_mode) return require_once(dirname(__FILE__) . DS . 'cli-process.php');

// Show image here
if (isset($_GET['img'])) {
    \BossBaby\Utility::func_show_image($_GET['img']);
}

// Get site info
if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-site') {
	$arr = array(
		'site_url' => dirname(strtok(\BossBaby\Utility::func_get_current_url(), '?')) . '/',
		'favicon' => SELF_URL_NO_SCRIPT  . 'process.php?img=favicon',
	);
	header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

// Start session
if (!session_id()) @session_start();

// Check login
if (!isset($_SESSION['user_name']) or !$_SESSION['user_name']) {
	echo('Redirecting ...');
	\BossBaby\Utility::redirect('../login.php');
	exit;
}

// Check config to run
if (!$environment->enable)
	die('<p class="message">STOP!!!</p>');

$environment->bitmex_instance = new \Bitmex($environment->bitmex->{1}->apiKey, $environment->bitmex->{1}->apiSecret);
$environment->bitmex_instance2 = new \Bitmex($environment->bitmex->{2}->apiKey, $environment->bitmex->{2}->apiSecret);
$environment->bitmex_instance3 = new \Bitmex($environment->bitmex->{3}->apiKey, $environment->bitmex->{3}->apiSecret);

// ------------------------------------------------------------ //

if (count($_POST) > 0 and isset($_POST['rtype']) and $_POST['rtype'] == 'ajax' and isset($_POST['act']) and $_POST['act'] == 'put-order') {
    header('Content-Type: application/json');
    $res = array('code'=>'OK','desc'=>"DONE");
    echo json_encode($res);
    exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-current-price') {
	$arr = \BossBaby\Bitmex::func_get_current_price($environment->bitmex_instance);

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

	\BossBaby\Utility::func_print_arr_to_table($arr);

	require_once(LIB_DIR . DS . "bittrex-api/Bittrex.php");
	// $bittrex = new \Bittrex($environment->bittrex->{1}->apiKey, $environment->bittrex->{1}->apiSecret);
	// $responce = $bittrex->GetBalances();
	// dump($responce);

	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-chart') {
	?>
	<?php /*
	<div class="btcwdgt-chart">
		Loading chart ...
		<script type="text/javascript">
		(function(b,i,t,C,O,I,N) {
			window.addEventListener('load',function() {
			if(b.getElementById(C))return;
			I=b.createElement(i),N=b.getElementsByTagName(i)[0];
			I.src=t;I.id=C;N.parentNode.insertBefore(I, N);
			},false)
		})(document,'script','https://widgets.bitcoin.com/widget.js','btcwdgt');
		</script>
	</div>
	*/ ?>
	<div class="chart-content" id="tradingview_88629">
		Loading chart ...
		<!-- <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script> -->
		<script type="text/javascript">
			new TradingView.widget(
			{
				"width": '100%',
				"height": 570,
				"autosize": true,
				"symbol": "BITMEX:XBTUSD",
				"interval": "60",
				"container_id": "tradingview_88629",
				"timezone": "Asia/Hong_Kong",
				"theme": "Light",
				"style": "8",
				"locale": "en",
				"toolbar_bg": "#f1f3f6",
				"enable_publishing": false,
				"allow_symbol_change": true,
				"studies": [
					"BB@tv-basicstudies",
					// "IchimokuCloud@tv-basicstudies",
					// "MASimple@tv-basicstudies",
				],
				"show_popup_button": true,
				"popup_width": "1150",
				"popup_height": "650",
			}
		);
		</script>
	</div>
	<?php 
	exit;
}

// Process load
if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-account') {
	$arr = \BossBaby\Bitmex::func_get_account_info($environment->bitmex->{2}->email, $environment->bitmex->{2}->apiKey, $environment->bitmex->{2}->apiSecret);
	\BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-account2') {
	$arr = \BossBaby\Bitmex::func_get_account_info($environment->bitmex->{3}->email, $environment->bitmex->{3}->apiKey2, $environment->bitmex->{3}->apiSecret2);
	\BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-wallet') {
	$tmp = \BossBaby\Bitmex::func_get_account_wallet($environment->bitmex_instance2);
	$arr = array(
		'amount' => ($tmp['amount'] * 0.00000001) . ' BTC',
		'amountFund' => '~' . round(($tmp['amount'] * 0.00000001) * ($_SESSION['getTicker']['last']), 3) . ' USD',
		'account' => $tmp['account'],
		'currency' => $tmp['currency'],
		'prevDeposited' => $tmp['prevDeposited'],
		'prevAmount' => $tmp['prevAmount'],
		'deposited' => $tmp['deposited'],
		// 'amount' => $tmp['amount'],
		'withdrawn' => $tmp['withdrawn'],
	);
	\BossBaby\Utility::func_print_arr_to_table($arr);//, 'Current Wallet'
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-wallet2') {
	$tmp = \BossBaby\Bitmex::func_get_account_wallet($environment->bitmex_instance3);
	$arr = array(
		'amount' => ($tmp['amount'] * 0.00000001) . ' BTC',
		'amountFund' => '~' . round(($tmp['amount'] * 0.00000001) * ($_SESSION['getTicker']['last']), 3) . ' USD',
		'account' => $tmp['account'],
		'currency' => $tmp['currency'],
		'prevDeposited' => $tmp['prevDeposited'],
		'prevAmount' => $tmp['prevAmount'],
		'deposited' => $tmp['deposited'],
		// 'amount' => $tmp['amount'],
		'withdrawn' => $tmp['withdrawn'],
	);
	\BossBaby\Utility::func_print_arr_to_table($arr);//, 'Current Wallet'
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-open-positions') {
	$arr = \BossBaby\Bitmex::func_get_open_positions($environment->bitmex_instance2);
	// \BossBaby\Utility::func_print_arr_to_table($arr);
	
	if (is_array($arr) and count($arr) > 0) {
		foreach ($arr as $key => $tmp) {
			// \BossBaby\Utility::func_print_arr_to_table($tmp);
			
			$arr = array(
				'openingQty' => $tmp['openingQty'],
				'leverage' => $tmp['leverage'],
				'marginType' => ($tmp['liquidationPrice'] < $tmp['avgEntryPrice']) ? 'LONG' : 'SHORT',
				'realisedPnl' => $tmp['realisedPnl'],
				'unrealisedGrossPnl' => $tmp['unrealisedGrossPnl'],
				'unrealisedPnlPcnt' => $tmp['unrealisedPnlPcnt'],
				'unrealisedRoePcnt' => $tmp['unrealisedRoePcnt'],
				'avgCostPrice' => $tmp['avgCostPrice'],
				'avgEntryPrice' => $tmp['avgEntryPrice'],
				'breakEvenPrice' => $tmp['breakEvenPrice'],
				'marginCallPrice' => $tmp['marginCallPrice'],
				'liquidationPrice' => $tmp['liquidationPrice'],
				'isOpen' => $tmp['isOpen'],
				'initMarginReq' => $tmp['initMarginReq'],
				// 'markPrice' => $tmp['markPrice'],
				// 'commission' => $tmp['commission'],
				// 'maintMarginReq' => $tmp['maintMarginReq'],
			);
			if ($arr['realisedPnl'] < 0) $arr['realisedPnl'] = '<span class="text-danger">' . number_format(($tmp['realisedPnl'] * 0.00000001), 8) . ' XBT</span>';
			else $arr['realisedPnl'] = '<span class="text-success">' . number_format(($tmp['realisedPnl'] * 0.00000001), 8) . ' XBT</span>';
			if ($arr['unrealisedGrossPnl'] < 0) $arr['unrealisedGrossPnl'] = '<span class="text-danger">' . number_format(($tmp['unrealisedGrossPnl'] * 0.00000001), 8) . ' XBT</span>';
			else $arr['unrealisedGrossPnl'] = '<span class="text-success">' . number_format(($tmp['unrealisedGrossPnl'] * 0.00000001), 8) . ' XBT</span>';
			if ($arr['unrealisedPnlPcnt'] < 0) $arr['unrealisedPnlPcnt'] = '<span class="text-danger">' . ($tmp['unrealisedPnlPcnt'] * 100) . '%</span>';
			else $arr['unrealisedPnlPcnt'] = '<span class="text-success">' . ($tmp['unrealisedPnlPcnt'] * 100) . '%</span>';
			if ($arr['unrealisedRoePcnt'] < 0) $arr['unrealisedRoePcnt'] = '<span class="text-danger">' . ($tmp['unrealisedRoePcnt'] * 100) . '%</span>';
			else $arr['unrealisedRoePcnt'] = '<span class="text-success">' . ($tmp['unrealisedRoePcnt'] * 100) . '%</span>';
			$arr['leverage'] = '<span class="text-warning"><b>x' . $tmp['leverage'] . '</b></span>';
			$arr['avgEntryPrice'] = '<span class="text-success">' . $tmp['avgEntryPrice'] . '</span>';
			$arr['liquidationPrice'] = '<span class="text-danger">' . $tmp['liquidationPrice'] . '</span>';
			$arr['marginType'] = ($arr['marginType'] == 'LONG') ? '<span class="text-success">' . $arr['marginType'] . '</span>' : '<span class="text-danger">' . $arr['marginType'] . '</span>';
			$arr['initMarginReq'] = round($tmp['initMarginReq'], 2);

			\BossBaby\Utility::func_print_arr_to_table($arr);
		}
	}
	else \BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-open-positions2') {
	$arr = \BossBaby\Bitmex::func_get_open_positions($environment->bitmex_instance3);
	// \BossBaby\Utility::func_print_arr_to_table($arr);
	
	if (is_array($arr) and count($arr) > 0) {
		foreach ($arr as $key => $tmp) {
			// \BossBaby\Utility::func_print_arr_to_table($tmp);
			
			$arr = array(
				'openingQty' => $tmp['openingQty'],
				'leverage' => $tmp['leverage'],
				'marginType' => ($tmp['liquidationPrice'] < $tmp['avgEntryPrice']) ? 'LONG' : 'SHORT',
				'realisedPnl' => $tmp['realisedPnl'],
				'unrealisedGrossPnl' => $tmp['unrealisedGrossPnl'],
				'unrealisedPnlPcnt' => $tmp['unrealisedPnlPcnt'],
				'unrealisedRoePcnt' => $tmp['unrealisedRoePcnt'],
				'avgCostPrice' => $tmp['avgCostPrice'],
				'avgEntryPrice' => $tmp['avgEntryPrice'],
				'breakEvenPrice' => $tmp['breakEvenPrice'],
				'marginCallPrice' => $tmp['marginCallPrice'],
				'liquidationPrice' => $tmp['liquidationPrice'],
				'isOpen' => $tmp['isOpen'],
				'initMarginReq' => $tmp['initMarginReq'],
				// 'markPrice' => $tmp['markPrice'],
				// 'commission' => $tmp['commission'],
				// 'maintMarginReq' => $tmp['maintMarginReq'],
			);
			if ($arr['realisedPnl'] < 0) $arr['realisedPnl'] = '<span class="text-danger">' . number_format(($tmp['realisedPnl'] * 0.00000001), 8) . ' XBT</span>';
			else $arr['realisedPnl'] = '<span class="text-success">' . number_format(($tmp['realisedPnl'] * 0.00000001), 8) . ' XBT</span>';
			if ($arr['unrealisedGrossPnl'] < 0) $arr['unrealisedGrossPnl'] = '<span class="text-danger">' . number_format(($tmp['unrealisedGrossPnl'] * 0.00000001), 8) . ' XBT</span>';
			else $arr['unrealisedGrossPnl'] = '<span class="text-success">' . number_format(($tmp['unrealisedGrossPnl'] * 0.00000001), 8) . ' XBT</span>';
			if ($arr['unrealisedPnlPcnt'] < 0) $arr['unrealisedPnlPcnt'] = '<span class="text-danger">' . ($tmp['unrealisedPnlPcnt'] * 100) . '%</span>';
			else $arr['unrealisedPnlPcnt'] = '<span class="text-success">' . ($tmp['unrealisedPnlPcnt'] * 100) . '%</span>';
			if ($arr['unrealisedRoePcnt'] < 0) $arr['unrealisedRoePcnt'] = '<span class="text-danger">' . ($tmp['unrealisedRoePcnt'] * 100) . '%</span>';
			else $arr['unrealisedRoePcnt'] = '<span class="text-success">' . ($tmp['unrealisedRoePcnt'] * 100) . '%</span>';
			$arr['leverage'] = '<span class="text-warning"><b>x' . $tmp['leverage'] . '</b></span>';
			$arr['avgEntryPrice'] = '<span class="text-success">' . $tmp['avgEntryPrice'] . '</span>';
			$arr['liquidationPrice'] = '<span class="text-danger">' . $tmp['liquidationPrice'] . '</span>';
			$arr['marginType'] = ($arr['marginType'] == 'LONG') ? '<span class="text-success">' . $arr['marginType'] . '</span>' : '<span class="text-danger">' . $arr['marginType'] . '</span>';
			$arr['initMarginReq'] = round($tmp['initMarginReq'], 2);

			\BossBaby\Utility::func_print_arr_to_table($arr);
		}
	}
	else \BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-open-order') {
	$arr = \BossBaby\Bitmex::func_get_open_orders($environment->bitmex_instance2);
	// \BossBaby\Utility::func_print_arr_to_table($arr);

	if (is_array($arr) and count($arr) > 0) {
		foreach ($arr as $key => $tmp) {
			// \BossBaby\Utility::func_print_arr_to_table($tmp);
			
			$arr = array(
				// 'orderID' => $tmp['orderID'],
				'side' => $tmp['side'],
				'orderQty' => $tmp['orderQty'],
				'price' => $tmp['price'],
				'ordType' => $tmp['ordType'],
				'timeInForce' => $tmp['timeInForce'],
				'execInst' => $tmp['execInst'],
				'ordStatus' => $tmp['ordStatus'],
			);
			\BossBaby\Utility::func_print_arr_to_table($arr);
		}
	}
	else \BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-open-order2') {
	$arr = \BossBaby\Bitmex::func_get_open_orders($environment->bitmex_instance3);
	// \BossBaby\Utility::func_print_arr_to_table($arr);

	if (is_array($arr) and count($arr) > 0) {
		foreach ($arr as $key => $tmp) {
			// \BossBaby\Utility::func_print_arr_to_table($tmp);
			
			$arr = array(
				// 'orderID' => $tmp['orderID'],
				'side' => $tmp['side'],
				'orderQty' => $tmp['orderQty'],
				'price' => $tmp['price'],
				'ordType' => $tmp['ordType'],
				'timeInForce' => $tmp['timeInForce'],
				'execInst' => $tmp['execInst'],
				'ordStatus' => $tmp['ordStatus'],
			);
			\BossBaby\Utility::func_print_arr_to_table($arr);
		}
	}
	else \BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-margin') {
	$tmp = \BossBaby\Bitmex::func_get_margin($environment->bitmex_instance2);
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
	\BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-margin2') {
	$tmp = \BossBaby\Bitmex::func_get_margin($environment->bitmex_instance3);
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
	\BossBaby\Utility::func_print_arr_to_table($arr);
	exit;
}


if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-actions') {
	$current = $environment->bitmex->getTicker();
	// $price = $current['last'];
	$price = 7433;
	$arr = array(
		'leverage' => 5,
		'ordType' => 'Limit', // Limit | Market
		'side' => 'Sell', // Buy | Sell
		'price' => $price,
		'orderQty' => 1,
	);
	\BossBaby\Utility::func_print_arr_to_table($arr, 'Place Order');

	// $environment->bitmex->setLeverage($arr['leverage']);
	// $arr = $environment->bitmex->createOrder($arr['ordType'], $arr['side'], (int) $arr['price'], (int) $arr['orderQty']);
	// \BossBaby\Utility::func_print_arr_to_table($arr, 'Place Order');

	$arr = $environment->bitmex->cancelAllOpenOrders('note to all closed orders at ' . date('H:i:s d/m/Y'));
	dump($arr); die;
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-margin') {
	$tmp = $environment->bitmex->getMargin();
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
	\BossBaby\Utility::func_print_arr_to_table($arr, 'Margin');
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-orderbook') {
	$arr = $environment->bitmex->getOrderBook($depth = 25);
	// \BossBaby\Utility::func_print_arr_to_table($arr, 'OrderBook');
	if ($arr) {
		foreach ($arr as $key => $value) {
			\BossBaby\Utility::func_print_arr_to_table($value);
		}
	}
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-orders') {
	$arr = $environment->bitmex->getOrders(100);
	\BossBaby\Utility::func_print_arr_to_table($arr, 'List User Order');
	exit;
}

if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-order') {
	$j = 0;
	for ($i=0; $i < 10; $i++) {
		$arr = $environment->bitmex->getOrder($orderID = $i, $count = 100);
		if (!$arr) continue;
		$arr['$i'] = $i;
		$arr['$j'] = $j;
		if ($j>0 and ($j)%3==0) \BossBaby\Utility::func_print_arr_to_table($arr, 'Order', array('style' => 'clear:both;'));
		else \BossBaby\Utility::func_print_arr_to_table($arr, 'Order');
		$j++;
	}
	exit;
}

die('NOT IN AJAX MODE!!!');