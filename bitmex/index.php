<?php
// dump(__FILE__);
require_once ("header.php");
// ============================================================ //

require_once ("bitmex-api/BitMex.php");

$account = 'signvltk1@gmail.com';
$apiKey = 'P5RaBUJ-8NZsxG_E5x5p6C_B';
$apiSecret = 'FZ-zqEpiqVPlHOtBu4rMbwx26ZeRoZbQ-RzSiyGv6E9c9epy';
$bitmex = new BitMex($apiKey, $apiSecret);
?>
<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Main Info</h3></div>
	<div class="panel-body">
		<table class="table table-bordered table-condensed">
			<tr>
				<td><label>Account:</label></td>
				<td><?php echo $account; ?></td>
			</tr>
			<tr>
				<td><label>API Key:</label></td>
				<td><?php echo $apiKey; ?></td>
			</tr>
			<tr>
				<td><label>API Secret:</label></td>
				<td><?php echo $apiSecret; ?></td>
			</tr>
		</table>
	</div>
</div>

<?php
$current = $bitmex->getTicker();
print_arr1_to_table('Current Price', $current);

$tmp = $bitmex->getWallet();
$arr = array(
	'account' => $tmp['account'],
	'currency' => $tmp['currency'],
	'prevDeposited' => $tmp['prevDeposited'],
	'prevAmount' => $tmp['prevAmount'],
	'deposited' => $tmp['deposited'],
	'amount' => $tmp['amount'],
	'withdrawn' => $tmp['withdrawn'],
);
print_arr1_to_table('Current Wallet', $arr);

$arr = $bitmex->getOpenOrders();
print_arr1_to_table('List Order', $arr);

$arr = $bitmex->getOpenPositions();
print_arr1_to_table('Open Positions', $arr);
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
	print_arr1_to_table('', $arr);
}

// $bitmex->closePosition($price);
// $bitmex->editOrderPrice($orderID, $price);

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
print_arr1_to_table('Margin', $arr);

// $price = $current['last'];
$price = 6401;
$arr = array(
	'leverage' => 5,
	'ordType' => 'Limit', // Limit | Market
	'side' => 'Buy', // Buy | Sell
	'price' => $price,
	'orderQty' => 1,
);
print_arr1_to_table('Place Order', $arr);

// $bitmex->setLeverage($arr['leverage']);
// $arr = $bitmex->createOrder($arr['ordType'], $arr['side'], (int) $arr['price'], (int) $arr['orderQty']);
// print_arr1_to_table('Place Order', $arr);

// $arr = $bitmex->getOrderBook($depth = 25);
// print_arr1_to_table('OrderBook', $arr);

$arr = $bitmex->getOrders(100);
print_arr1_to_table('List User Order', $arr);

$arr = $bitmex->getOrder($orderID = 0, $count = 100);
print_arr1_to_table('Order', $arr);

// $bitmex->cancelAllOpenOrders('note to all closed orders at ' . date('H:i:s d/m/Y'));
// dump($arr); die;

// ============================================================ //
require_once ("footer.php");