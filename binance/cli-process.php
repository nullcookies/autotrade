<?php
chdir(__DIR__);
defined('IS_VALID') or define('IS_VALID', 1);
require_once("../main.php");
// require_once(LIB_DIR . DS . "bitmex-api/BitMex.php");
require_once(dirname(__FILE__) . DS . "function.php");

// Detect run as CLI mode
if (!$cli_mode) return \Utility::func_redirect('index.php');

// Get global variables
$environment = new stdClass();

$config_file = dirname(__FILE__) . DS . "config.php";
$config = \Utility::func_read_config($config_file);
if (is_array($config) and count($config)) {
	foreach ($config as $key => $value) {
		$environment->$key = $value;
	}
}

// ------------------------------------------------------------ //
