<?php
// Start session
if (!session_id()) @session_start();

// ------------------------------------------------------------ //

if (isset($_SESSION['user_name']) and $_SESSION['user_name']) {
	echo '<a href="bitmex/">Bitmex</a>';
	echo '<br/><a href="binance/">Binance</a>';
	echo '<br/><a href="bittrex/">Bittrex</a>';
}
else {
	echo 'Silence is golden!!!';
}
?>