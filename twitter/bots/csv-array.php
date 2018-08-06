<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once __DIR__ . '/../../main.php';

// Load composer
require_once LIB_DIR . '/twitter/vendor/autoload.php';

$arr = [];
$csv_file = 'D:\Projects\mine\autotrade\test.csv';
$handle = fopen($csv_file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        
        $lines = explode(',', $line);
        $coin = trim($lines[0]);
        $url = trim($lines[1]);
        $name = trim($lines[2]);
        
        $arr[$coin] = array('root_url' => $url, 'username' => $name);

        if ($arr[$coin]['username'] == '') {
        	$tmp = explode('/', $url);
        	$arr[$coin]['username'] = $tmp[3];
        }

        if (strpos($arr[$coin], '@') !== false) {
        	$tmp = trim(str_replace('@', '', $arr[$coin]['username']));
        	$arr[$coin]['username'] = $tmp;
        }
    }
    fclose($handle);
} else {
    // error opening the file.
}
print_r($arr);
$file = ROOT_DIR . '/arr.php';
\BossBaby\Config::write_file($file, $arr);
