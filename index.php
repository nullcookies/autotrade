<?php
// Start session
if (!session_id()) @session_start();

// ------------------------------------------------------------ //

if (isset($_SESSION['user_name']) and $_SESSION['user_name']) {
	echo '<style type="text/css">body{font-size:100px;}</style>';
	echo '<br/><a href="bitmex/">Bitmex</a>';
	echo '<br/><br/><a href="binance/">Binance</a>';
	echo '<br/><br/><a href="bittrex/">Bittrex</a>';
}
else {
	echo 'Silence is golden!!!';
}
?>