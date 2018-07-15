<?php
if (!defined('IS_VALID')) die('Access denied.' . "\n");

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('ROOT_DIR') or define('ROOT_DIR', dirname(__FILE__));
defined('LOGS_DIR') or define('LOGS_DIR', ROOT_DIR . DS . 'logs');
defined('CONFIG_FILE') or define('CONFIG_FILE', ROOT_DIR . DS . 'config.php');

// Detect run as CLI mode
$cli_mode = (php_sapi_name() == "cli") ? true : false;
defined('CLI_MODE') or define('CLI_MODE', $cli_mode);

/* AJAX check  */
$ajax_mode = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
defined('AJAX_MODE') or define('AJAX_MODE', $ajax_mode);

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Report all errors except E_NOTICE and E_WARNING
if (!is_dir(LOGS_DIR)) mkdir(LOGS_DIR, 0777);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set("log_errors", 1);
ini_set("error_log", LOGS_DIR . DS . date("Ymd") . "-log.txt");

// ------------------------------------------------------------ //

function func_read_config()
{
	defined('CONFIG_FILE') or define('CONFIG_FILE', ROOT_DIR . DS . 'config.php');
	if (!is_file(CONFIG_FILE) or !file_exists(CONFIG_FILE))
		return array();
	$arr = parse_ini_file(CONFIG_FILE);
	return $arr;
}

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

function func_get_margin($account_info = null)
{
	if (!$account_info) return array();

	$arr = $account_info->getMargin();
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

function func_get_client_ip($checkProxy = true)
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = @$_SERVER['REMOTE_ADDR'];
    }
    
    if (!$ip)
        $ip = '127.0.0.1';
    
    return $ip;
}

function func_write_log($string = null, $file = null)
{
    try {
        $log_file = $file ?: LOGS_DIR . DS . date("Ymd") . "-log.txt";
        $str      = '';
        $str .= date('Y-m-d H:i:s') . "\t" . func_get_client_ip();
        $str .= "\t" . $string . "\r\n";
        $str = strval($str);
        $fp  = fopen($log_file, 'a');
        fwrite($fp, $str);
        return fclose($fp);
    }
    catch (Exception $e) {
        return true;
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

/**
 * List of query parameters that get automatically dropped when rebuilding
 * the current URL.
 */
$DROP_QUERY_PARAMS = array('code', 'state', 'signed_request');

/**
 * Returns true if and only if the key or key/value pair should
 * be retained as part of the query string.  This amounts to
 * a brute-force search of the very small list of Facebook-specific
 * params that should be stripped out.
 *
 * @param string $param A key or key/value pair within a URL's query (e.g.
 *                     'foo=a', 'foo=', or 'foo'.
 *
 * @return boolean
 */
function func_should_retain_param($param)
{
    foreach ($DROP_QUERY_PARAMS as $drop_query_param) {
        if (strpos($param, $drop_query_param . '=') === 0) {
            return false;
        }
    }
    
    return true;
}

/**
 * Returns the Current URL, stripping it of known FB parameters that should
 * not persist.
 *
 * @return string The current URL
 */
function func_get_current_url()
{
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $parts      = parse_url($currentUrl);
    
    $query = '';
    if (!empty($parts['query'])) {
        // drop known fb params
        $params          = explode('&', $parts['query']);
        $retained_params = array();
        foreach ($params as $param) {
            if (func_should_retain_param($param)) {
                $retained_params[] = $param;
            }
        }
        
        if (!empty($retained_params)) {
            $query = '?' . implode($retained_params, '&');
        }
    }
    
    // use port if non default
    $port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';
    
    // rebuild
    return $protocol . $parts['host'] . $port . $parts['path'] . $query;
}
