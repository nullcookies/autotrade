<?php
if (!defined('STDIN')) die('Access denied.' . "\n");

// chdir(__DIR__);
// defined('IS_VALID') or define('IS_VALID', 1);
// require_once __DIR__ . '/../../main.php';

// Error handle
require_once __DIR__ . '/error-handle.php';

// $result = async_curl_request('/test.php');
// $result = async_execute_file(__DIR__ . '/test.php');

// Check bots status
async_execute_file(__DIR__ . '/status.php');

// Run cron retrive coins on Binance
async_execute_file(__DIR__ . '/boss_baby_xbot/coin-pulse-binance.php');

// Run cron retrive coins on Binance
async_execute_file(__DIR__ . '/boss_baby_xbot/coin-pulse-bittrex.php');

// Run cron retrive tweets on Twitter
async_execute_file(__DIR__ . '/boss_baby_xbot/load-tweets.php');

// Finished
die('FINISHED');

// ------------------------------------------------------------ //

function async_execute_file($file = null, $debug = false) {
	try {
		if (!is_file($file) or !file_exists($file)) {
			\BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::error::file does not exist');
			throw new Exception("File $file does not exist");
		}

		$file = trim($file);
		$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
		$output = shell_exec('php ' . $file . ' > /dev/null 2>/dev/null &');

		if ($debug) {
			// dump($output);
			\BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::output::' . serialize($output));
		}

		return $output;
	}
	catch(Exception $e) {
		if ($debug) {
			\BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::error::' . serialize($e));
		}

		throw new Exception($e->getMessage());
	}
}

function async_stream_request($url, $data = null, $optional_headers = null, $getresponse = false) {
    $params = array(
        'http' => array(
            'method' => 'GET',
            'content' => $data
        )
    );

    if ($optional_headers !== null) {
         $params['http']['header'] = $optional_headers;
    }

    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);

    if (!$fp) {
        return false;
    }

    if ($getresponse) {
        $response = stream_get_contents($fp);
        return $response;
    }

    return true;
}

function async_curl_request($url, $data = null, $optional_headers = null, $getresponse = false) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	$response = curl_exec($ch);

    return $response;
}