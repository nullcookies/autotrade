<?php
if (!defined('IS_VALID')) die('Access denied.' . "\n");

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('ROOT_DIR') or define('ROOT_DIR', dirname(__FILE__));
defined('LOGS_DIR') or define('LOGS_DIR', ROOT_DIR . DS . 'logs');

if (!is_dir(LOGS_DIR)) mkdir(LOGS_DIR, 0777);

// Detect run as CLI mode
$cli_mode = (php_sapi_name() == "cli") ? true : false;
defined('CLI_MODE') or define('CLI_MODE', $cli_mode);

/* AJAX check  */
$ajax_mode = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
defined('AJAX_MODE') or define('AJAX_MODE', $ajax_mode);

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Report all errors except E_NOTICE and E_WARNING
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set("log_errors", 1);
ini_set("error_log", LOGS_DIR . DS . date("Ymd") . "-log.txt");

// ------------------------------------------------------------ //

require_once ("library/bitmex-api/BitMex.php");

global $environment;
$environment = new stdClass();
$environment->can_run = true;

$environment->account = 'long.vu0104@gmail.com';
$environment->apiKey = 'q1KYRfGHroeROIjRvdsvhJqv';
$environment->apiSecret = 'iCiuNYv_F4rdZkkc2R89bzMLb5KkkINkIkXHpEnN8sp1DEi3';
// $environment->bitmex = new BitMex($environment->apiKey, $environment->apiSecret);
$environment->bitmex = null;

$environment->account2 = 'signvltk1@gmail.com';
$environment->apiKey2 = 'P5RaBUJ-8NZsxG_E5x5p6C_B';
$environment->apiSecret2 = 'FZ-zqEpiqVPlHOtBu4rMbwx26ZeRoZbQ-RzSiyGv6E9c9epy';
// $environment->bitmex2 = new BitMex($environment->apiKey2, $environment->apiSecret2);
$environment->bitmex2 = null;

// ------------------------------------------------------------ //

function func_get_current_price()
{
	global $environment;
	if (is_null($environment->bitmex)) $environment->bitmex = new BitMex($environment->apiKey, $environment->apiSecret);
	$arr = $environment->bitmex->getTicker();
	if ($arr) {
		$arr['marketPrice'] = $arr['market_price'];
		unset($arr['market_price']);
	}
	return $arr;
}

function func_get_account_info($account = null, $apiKey = null, $apiSecret = null, $hide_apiSecret = true)
{
	$arr = array(
		'Account' => $account,
		'API Key' => $apiKey,
		'API Secret' => ($hide_apiSecret) ? func_replace_by_star($apiSecret) : $apiSecret,
	);
	return $arr;
}

function func_get_account_wallet($account_info = null)
{
	if (!$account_info) return array();

	$arr = $account_info->getWallet();
	return $arr;
}

function func_get_open_orders($account_info = null)
{
	if (!$account_info) return array();

	$arr = $account_info->getOpenOrders();
	return $arr;
}

function func_get_open_positions($account_info = null)
{
	if (!$account_info) return array();

	$arr = $account_info->getOpenPositions();
	return $arr;
}

// ------------------------------------------------------------ //

if (!function_exists("dump")) {
	function dump($arr)
	{
		echo "<pre>";
		var_dump($arr);
		echo "</pre>";
	}
}

function func_check_login($user = null, $pass = null)
{
	$arr_allowed = array(
		'admin' => md5(base64_encode('admin')), 
	);

	if (!$arr_allowed) return null;
	foreach ($arr_allowed as $key => $value) {
		if ($user == $key and md5(base64_encode($pass)) == $value) return $key;
	}
	return null;
}

function func_redirect($url = '/', $time = 0)
{
	echo '<meta http-equiv="refresh" content="' . (int) $time . ';url=' . (string) trim($url) . '"/>';
	exit;
}

function func_print_arr_to_table($arr = null, $title = '', $extra = null) 
{
	// if (!isset($extra['show_message'])) $extra['show_message'] = 1;
	if (!$arr) {echo '<p class="message">No data found!</p>'; exit;}
?>
	<?php /*<div class="panel <?php if (isset($extra['panel'])){echo $extra['panel'];}else echo 'panel-default';?>">
		<?php if ($title): ?><div class="panel-heading"><h3 class="panel-title"><?php echo $title; ?></h3></div><?php endif; ?>
		<div class="panel-body">*/ ?>
			<table class="table table-bordered table-condensed" <?php if (isset($extra['style'])){echo 'style="' . $extra['style'] . '"';}?>>
				<?php
				if (trim(strlen($title)) > 0) echo '<tr><td class="bg-info" colspan="2">' . $title . '</td></tr>';
				$i=0;
				foreach ($arr as $key => $value) {
					// echo '<tr><td><strong>' . $key . '</strong></td><td><strong>' . ((!is_array($value)) ? $value : var_export($value)) . '</strong></td></tr>';
					echo '<tr><td' . (($i == 0) ? ' class="col-title' . ((trim(strlen($title)) < 0) ? ' bg-info':'') . '"' : '') . '>' . $key . '</td><td' . (($i == 0) ? ' class="col-info' . ((trim(strlen($title)) < 0) ? ' bg-info':'') . '"' : '') . '><strong>' . ((!is_array($value)) ? $value : json_encode($value)) . '</strong></td></tr>';
					$i++;
				}
				?>
			</table>
		<?php /*</div>
	</div>*/ ?>
<?php
}

function func_replace_by_star($str = null)
{
	$str_new = '';
	for ($i=0; $i < strlen($str) - 1; $i++) { 
		$str_new .= '*';
	}
	return $str_new;
}

function func_cli_print_arr($arr = null, $title = '', $extra = null) 
{
	if (!$arr) {echo 'No data found!'; exit;}
	$max_key_length = func_max_key_length($arr);
	$max_value_length = func_max_value_length($arr);

	echo func_fill_space(' ', $max_key_length + $max_value_length + 5, '-') . "\n";
	foreach ($arr as $key => $value) {
		if (is_object($value)) {
			$max_value_length = strlen(serialize($value));
			echo '| ' . func_fill_space($key, $max_key_length) . '| ' . serialize($value) . ' |' . "\n";
		}
		else {
			if (strpos($value, '▲') !== false or strpos($value, '▼') !== false)
				$value .= '   ';
			if ((strpos($value, '▲') !== false or strpos($value, '▼') !== false) and strpos($value, '%') === false)
				$value .= ' ';
			if ((strpos($value, '▲') !== false or strpos($value, '▼') !== false) and strpos($value, '.') === false and strpos($value, '%') === false)
				$value .= ' ';
			
			echo '| ' . func_fill_space($key, $max_key_length) . '| ' . func_fill_space($value, $max_value_length) . ' |' . "\n";
		}
	}
	echo func_fill_space(' ', $max_key_length + $max_value_length + 5, '-') . "\n";
}

function func_max_key_length($arr = null) 
{
	if (!$arr) return 0;
	$max = 0;
	foreach ($arr as $key => $value) {
		if (strlen($key) > $max)
			$max = strlen($key);
	}
	return $max + 1;
}

function func_max_value_length($arr = null) 
{
	if (!$arr) return 0;
	$max = 0;
	foreach ($arr as $key => $value) {
		if (!is_string($value)) continue;
		if (strlen($value) > $max)
			$max = strlen($value);
	}
	return $max + 1;
}

function func_fill_space($str = null, $length = 0, $replace_with = ' ')
{
	if (!$str or !$length) return $str;
	$str_new = $str;
	for ($i=0; $i < ($length - strlen($str)); $i++) { 
		$str_new .= $replace_with;
	}
	return $str_new;
}


