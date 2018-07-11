<?php
if (!function_exists("dump")) {
	function dump($arr)
	{
		echo "<pre>";
		var_dump($arr);
		echo "</pre>";
	}
}

// ------------------------------------------------------------ //

dump(__FILE__);

// signvltk1:
// Id: V5H0706cB2dP4Y3Ifki1f54d
// Secret: yMp-3WuV24u2iFZDbEWUh_-FkYcutkQ_RMWVPIcvWBUjexeQ

$apiKey = 'V5H0706cB2dP4Y3Ifki1f54d';
$apiSecret = 'yMp-3WuV24u2iFZDbEWUh_-FkYcutkQ_RMWVPIcvWBUjexeQ';

#
# Simple GET
#
$verb = 'GET';
# Note url-encoding on querystring - this is '/api/v1/instrument?filter={"symbol": "XBTM15"}'
$path = '/api/v1/instrument';
$expires = 1518064236; # 2018-02-08T04:30:36Z
$data = '';

# HEX(HMAC_SHA256(apiSecret, 'GET/api/v1/instrument1518064236'))
# Result is:
# 'c7682d435d0cfe87c16098df34ef2eb5a549d4c5a3c2b1f0f77b8af73423bf00'
$signature = dechex(hash_hmac($apiSecret, $verb + $path + (string)$expires + $data));

dump($signature); die;

#
# GET with complex querystring (value is URL-encoded)
#
$verb = 'GET';
# Note url-encoding on querystring - this is '/api/v1/instrument?filter={"symbol": "XBTM15"}'
# Be sure to HMAC *exactly* what is sent on the wire
$path = '/api/v1/instrument?filter=%7B%22symbol%22%3A+%22XBTM15%22%7D';
$expires = 1518064237; # 2018-02-08T04:30:37Z
$data = '';

# HEX(HMAC_SHA256(apiSecret, 'GET/api/v1/instrument?filter=%7B%22symbol%22%3A+%22XBTM15%22%7D1518064237'))
# Result is:
# 'GET/api/v1/instrument?filter=%7B%22symbol%22%3A+%22XBTM15%22%7D1518064237'
$signature = dechex(hash_hmac($apiSecret, $verb + $path + (string)$expires + $data));

#
# POST
#
$verb = 'POST';
$path = '/api/v1/order';
$expires = 1518064238; # 2018-02-08T04:30:38Z
$data = '{"symbol":"XBTM15","price":219.0,"clOrdID":"mm_bitmex_1a/oemUeQ4CAJZgP3fjHsA","orderQty":98}';

# HEX(HMAC_SHA256(apiSecret, 'POST/api/v1/order1518064238{"symbol":"XBTM15","price":219.0,"clOrdID":"mm_bitmex_1a/oemUeQ4CAJZgP3fjHsA","orderQty":98}'))
# Result is:
# '1749cd2ccae4aa49048ae09f0b95110cee706e0944e6a14ad0b3a8cb45bd336b'
$signature = dechex(hash_hmac($apiSecret, $verb + $path + (string)$expires + $data));

