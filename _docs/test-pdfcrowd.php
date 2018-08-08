<?php
$url = "https://s.tradingview.com/widgetembed/?frameElementId=tradingview_1f234&symbol=BITMEX%3AXBTUSD&interval=60&symboledit=1&saveimage=1&toolbarbg=f1f3f6&studies=BB%40tv-basicstudies&theme=Dark&style=8&timezone=Asia%2FHo_Chi_Minh&showpopupbutton=1&studies_overrides=%7B%7D&overrides=%7B%7D&enabled_features=%5B%5D&disabled_features=%5B%5D&showpopupbutton=1&locale=en&utm_source=localhost&utm_medium=widget&utm_campaign=chart&utm_term=BITMEX%3AXBTUSD";
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, 'https://pdfcrowd.com/');
// curl_setopt($ch, CURLOPT_POST, 1);
// // curl_setopt($ch, CURLOPT_POSTFIELDS, "postvar1=value1&postvar2=value2&postvar3=value3");
// // In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//     http_build_query(array(
//         'noCache' => time(), 
//         'src' => $url, 
//         'conversion_source' => 'uri', 
//     ))
// );
// // Receive server response ...
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// echo $server_output = curl_exec($ch);
// // dump(curl_error($ch));
// curl_close($ch);
// Further processing ...
// dump($server_output);
// die;

set_time_limit(0);
require LIB_DIR . '/pdfcrowd-4.3.5/pdfcrowd.php';

try
{
    // create the API client instance
    $client = new \Pdfcrowd\HtmlToImageClient("johnboxer_cok", "784cf357a513d053120a24813d2e0d75");

    // configure the conversion
    $client->setOutputFormat("png");

    // run the conversion and write the result to a file
    $client->convertUrlToFile($url, __DIR__ . "/example.png");
}
catch(\Pdfcrowd\Error $why)
{
    // report the error
    error_log("Pdfcrowd Error: {$why}\n");

    // handle the exception here or rethrow and handle it at a higher level
    throw $why;
}

die;