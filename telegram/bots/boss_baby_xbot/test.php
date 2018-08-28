<?php
/**
 * README
 * This configuration file is intended to run a list of commands with crontab.
 * Uncommented parameters must be filled
 */

// Error handle
require_once __DIR__ . '/../error-handle.php';

dump(date('YmdHis', 1337004797));
dump(date('YmdHis', 1365846822));

global $environment;
$list_accounts = $environment->bitmex->accounts;
$list_id = [];
foreach ($list_accounts as $key => $account) {$list_id[] = $key;}
$rand = array_rand($list_id);
if ($rand <= 0) die('WRONG_ACCOUNT');
// $bitmex_instance = new \Bitmex($environment->bitmex->accounts->{$rand}->apiKey, $environment->bitmex->accounts->{$rand}->apiSecret);
// $arr = $bitmex_instance->getQuote('XBT');
// dump($arr);

// ------------------------------
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


// ------------------------------
// /* Get the port for the WWW service. */
// $service_port = getservbyname('www', 'tcp');

// /* Get the IP address for the target host. */
// $address = gethostbyname('wss://www.bitmex.com/realtime');

// /* Create a TCP/IP socket. */
// $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
// if ($socket === false) {
//     echo "socket_create() failed: reason: " . 
//          socket_strerror(socket_last_error()) . "\n";
// }

// echo "Attempting to connect to '$address' on port '$service_port'...";
// $result = socket_connect($socket, $address, $service_port);
// if ($result === false) {
//     echo "socket_connect() failed.\nReason: ($result) " . 
//           socket_strerror(socket_last_error($socket)) . "\n";
// }

// $in = "HEAD / HTTP/1.1\r\n";
// $in .= "Host: www.google.com\r\n";
// $in .= "Connection: Close\r\n\r\n";
// $out = '';

// echo "Sending HTTP HEAD request...";
// socket_write($socket, $in, strlen($in));
// echo "OK.\n";

// echo "Reading response:\n\n";
// while ($out = socket_read($socket, 2048)) {
//     echo $out;
// }

// socket_close($socket);

// ------------------------------

// require_once LIB_DIR . '/bitmex-api/api-connectors/auto-generated/php/SwaggerClient-php/vendor/autoload.php';

// $apiInstance = new Swagger\Client\Api\QuoteApi(
//     // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
//     // This is optional, `GuzzleHttp\Client` will be used as default.
//     new GuzzleHttp\Client()
// );
// $symbol = "XBT"; // string | Instrument symbol. Send a bare series (e.g. XBU) to get data for the nearest expiring contract in that series.  You can also send a timeframe, e.g. `XBU:monthly`. Timeframes are `daily`, `weekly`, `monthly`, `quarterly`, and `biquarterly`.
// $filter = ""; // string | Generic table filter. Send JSON key/value pairs, such as `{\"key\": \"value\"}`. You can key on individual fields, and do more advanced querying on timestamps. See the [Timestamp Docs](https://www.bitmex.com/app/restAPI#Timestamp-Filters) for more details.
// $columns = ""; // string | Array of column names to fetch. If omitted, will return all columns.  Note that this method will always return item keys, even when not specified, so you may receive more columns that you expect.
// $count = 100; // float | Number of results to fetch.
// $start = 0; // float | Starting point for results.
// $reverse = false; // bool | If true, will sort results newest first.
// $start_time = new \DateTime("2013-10-20T19:20:30+01:00"); // \DateTime | Starting date filter for results.
// $end_time = new \DateTime("2013-10-20T19:20:30+01:00"); // \DateTime | Ending date filter for results.

// try {
//     $result = $apiInstance->quoteGet($symbol, $filter, $columns, $count, $start, $reverse, $start_time, $end_time);
//     print_r($result);
// } catch (Exception $e) {
//     echo 'Exception when calling QuoteApi->quoteGet: ', $e->getMessage(), PHP_EOL;
// }
?>

<script type="text/javascript" src="http://localhost/mine/autotrade/library/bitmex-api/api-connectors/official-ws/socket.io.js"></script>
<script type="text/javascript">
	// var socket = io('wss://testnet.bitmex.com/realtime');
	// socket.on('open', function (data) {
	// 	console.log(data);
	// 	// socket.emit('my other event', { my: 'data' });
	// });
</script>