<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once("main.php");

// Start session
if (!session_id()) @session_start();

// Show image here
if (isset($_GET['img'])) {
    \BossBaby\Utility::func_show_image($_GET['img']);
}

// Get site info
if (count($_GET) > 0 and $ajax_mode and isset($_GET['act']) and $_GET['act'] == 'load-site') {
	$arr = array(
		'site_url' => dirname(strtok(\BossBaby\Utility::func_get_current_url(), '?')) . '/',
		'favicon' => SELF_URL_NO_SCRIPT  . 'index.php?img=favicon',
	);
	header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

// ------------------------------------------------------------ //

if (isset($_SESSION['user_name']) and $_SESSION['user_name']) {
	echo '<style type="text/css">body{font-size:50px;}</style>';
	echo '<br/><a href="bitmex/">Bitmex</a>';
	echo '<br/><br/><a href="binance/">Binance</a>';
	echo '<br/><br/><a href="bittrex/">Bittrex</a>';
}
else {
	echo 'Silence is golden!!!';

	phpinfo();
}
?>