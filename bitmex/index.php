<?php
// dump(__FILE__);
require_once ("header.php");
// ============================================================ //

require_once ("bitmex-api/BitMex.php");

$account = 'signvltk1@gmail.com';
$apiKey = 'P5RaBUJ-8NZsxG_E5x5p6C_B';
$apiSecret = 'FZ-zqEpiqVPlHOtBu4rMbwx26ZeRoZbQ-RzSiyGv6E9c9epy';
$bitmex = new BitMex($apiKey, $apiSecret);
// dump($bitmex->createOrder("Limit", "Sell", 50000, 1000));
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
$arr = $bitmex->getWallet();
print_arr1_to_table('Current Wallet', $arr);

$arr = $bitmex->getOpenOrders();
print_arr1_to_table('List Order', $arr);


// ============================================================ //
require_once ("footer.php");