<?php
if (!defined('IS_VALID')) die('Access denied.' . "\n");

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

function func_clean_path($path)
{
    $path = trim($path);
    $path = trim($path, '\\/');
    $path = str_replace(array('../', '..\\'), '', $path);
    if ($path == '..') {
        $path = '';
    }
    return str_replace('\\', '/', $path);
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

function func_show_image($img)
{
    $modified_time = gmdate('D, d M Y 00:00:00') . ' GMT';
    $expires_time = gmdate('D, d M Y 00:00:00', strtotime('+1 day')) . ' GMT';

    $img = trim($img);
    $images = func_get_images();
    $image = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAEElEQVR42mL4//8/A0CAAQAI/AL+26JNFgAAAABJRU5ErkJggg==';
    if (isset($images[$img])) {
        $image = $images[$img];
    }
    $image = base64_decode($image);
    if (function_exists('mb_strlen')) {
        $size = mb_strlen($image, '8bit');
    } else {
        $size = strlen($image);
    }

    if (function_exists('header_remove')) {
        header_remove('Cache-Control');
        header_remove('Pragma');
    } else {
        header('Cache-Control:');
        header('Pragma:');
    }

    header('Last-Modified: ' . $modified_time, true, 200);
    header('Expires: ' . $expires_time);
    header('Content-Length: ' . $size);
    header('Content-Type: image/png');
    echo $image;

    exit;
}

/**
 * Get base64-encoded images
 * @return array
 */
function func_get_images()
{
    return array(
        'favicon' => 'iVBORw0KGgoAAAANSUhEUgAAAfMAAAHyCAMAAADIjdfcAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAYBQTFRFcp1FVVVVgLFMX4Y7r6+wdKBGi8BQzc3OZY0/e3dzWGhJ0rGReqhJbphDYXNNk5SUeaZIfKpJaZJB++bRbJZCpI54cJpEdqNHapNC/Nm3VF9IS1NDdZtM+fb0+fHp++DEfaxKcpZM3+DgVnE9VGtAVng6e6pJZ49ASE1EdaJHd6RIVltRRkhEZ4BNTmI+Y19bWH04eaFNga9NeadIRUVFfahN/dKof61MjcJReqVN+fr7d59McJFLeKdHfKZNiLxPjMFRhbdOgLBMi79Qir5Qh7pPf69LhrlOg7VNeqdKjMFQg7RNgrNMir9QbYpNh7tPfq5Lfq1KgbJMhLZOW2JTgrNNib5QgrRNhrhOir5Pib1Ph7lOeKVIgrJMhLVNdaFHir1PUE1L8cmhvKCEinlqXFZR7u/w5sCbb2hilYJw/NStTVtBhbdP+uzehrdPf69KhbZPcI9Pg7ROfq5Kf7BLhLZPd6BLfKlLrMqNw9iuobaLUFBQlbxs5e7gf65KeaNM7DYFIwAAHpBJREFUeNrs3QljGsmVAOBqm6wMJJBZRZsMyAcCJkpkRNvpwWukHVaOr1l7sZ1RJnNkJtFpbI2z62wy3tt/PYAumqu7q95VpX4/QKb4XK9eva4u1D86E/88K/5lNP4uIn4djn89ic8HcX8ivuzHH7/qR2d2PDuJB+Px9ddfPwrHF/3YmhafDePbbx+PxJPT+F0/fjuIp6fxzTe/Gcal0bjXj4eDUCn5RSN3xzwlj0vujHlKHpvcFfOUPD65I+YpeQJyN8xT8iTkTpin5InIXTBPyZORO2Cekickt988JU9Kbr15Sp6Y/LlKyS8aueXmKbkGud3mKbkOudXmKbkWuc3mKbkeucXmKbkmub3mKbkuubXmKbk2+QuVkl80ckvNU3IDcjvNU3ITcivNU3IjchvNU3IzcgvNU3JDcvvMU3JTcuvMU3JjctvMU3Jz8hsqJb9o5HaZp+QQ5FaZp+Qg5DaZp+Qw5BaZp+RA5PaYp+RQ5NaYp+Rg5LaYp+Rw5JaYp+SA5HaYp+SQ5FaYp+Sg5DaYp+Sw5BaYp+TA5PLNU3JocvHmKTk4+QcqJb9o5MLNU3IE8o9USn7RyEWbp+Qo5JLNU3IccsHmKTkSuVzzlByLXKx5So5GLtU8JccjF2qekiOSy+zJpOSY5CLNU3JUconmKTkuuUDzlByZXF4Nl5Jjk4szT8nRyaWZp+T45MLMU3IC8h+qlPyikYsyT8lJyCWZp+Q05ILMU3IicjnmKTkVuRjzlJyMXIp5Sk5HLsQ8JSckl2GeklOSizBPyUnJJZin5LTkAsxTcmLyT1VKftHI2c1TcnJybvOUnJ78Vyolv2jkvOYpOQc5q3lKzkLOac5AfnvjPC4sOaM5KflGt1EqBWNRLhUalbsXjpzPnIz89mahHMyLUqHy54tEzmZORF6J8D6No0LlwpD/WDlMXimsBgkit/nnC0HOZE5AfrtRDhJHrnIByHnM8ck3CoFerBbuuE7OYo5OvlEKDCK37DY5hzk2+W0j8eEm7q3L5AzmyOR/LQQAMaruGjm9OTJ5ZTWAiTN158jJzXHJ/5oL4OJo2U3y3yuXyDdWA9Ao3XCRnNgcl7wRgEfjG/fIac1Ryb/LBQhRXnaOnNQcl/wowInCJcfIKc1RyT9eDbBiddktckJzW8lHprob5L9UKXmcbdt7h8jJzK0m7+f3rjvkVOao5LfRyYf53RVyInM7K/ax/P7OEXIac0zy727RkPfz+3s3yEnM0chvNUqrAWF0nSCnMEci/7hA6n28qLtATmCOQ755FHBEwQFyfHMU8s1ywBSl29aTo5tjkN8qBXxxdNt2cmxzBPI/NQLWOEO3lRzZHIH843IQiEC3lhzXHIG8G/DHEN1e8p8qq8j/VAgCGegWk2OaI5CXgkAIusXkiObw5LelkJ+gW0qOZw5P/vFqICeO7CVHM3ecPAgK1pJjmTtP3ke3lRzJHKF8KwfSomspOY65wxX7aGzaSY5ijtCKkUgerH5vJTmGOQJ5IRAZ5ec2kv9C2UC+GQiN0iULyeHNMR6rBGKjdsk+cnBzjIenZbnmQfWedeR/UOLJf50TTB7c9O/ZRg5sjkHeDUTH6uV7lpHDmmOQi+u/TXTe++hWkYOaoxx3LAXSo3b5L1aRQ5qjkDfEkwc36310i8gBzVHIPw4siLJ/+S8WkcOZ47y6ULLBPFj3Lz+0hxzMHIe8awV5P7v30a0hhzLHIf9u1Q7zfnb3L/+bLeRA5kivISZ4tLLTe7W0tMNXu5+hyyeHMUcivxX3Gz94lW8NIs+Z3Y/RLSAHMcd6vzxmAbd/DD6IPc7sPkC3gfwHSi55vCeo+y9bI8GHXh2i20AOYI5F/l2cx2m9fCsc+2x9d3+A/v8WkJubo10cEqMDt7PUmgg29PUh+n/IJzc2RyOPsU9r5lstOeiDMm6ILp3c1BzvRqjoad7bbU2NV1wP2PxjdOnkhuZ45NHTfL81K/YYy7g++gvh5GbmiPe+FfTJ2dDL/hi6THIjc0Ty2ybkrVaepyNXC6MLJTcxx7zdMWo1z7bmR77Jtl87Q5dKbmCOeqFnxGre3I0wb+1mGSf6EF0sub456rW9EdN8J9+KjiWOiV4/Q5dLrm2Oe1NzxDTfa8UJjkV93R9BF0qua876o1nZVrzY7TE1ZoboN8SSK4Hkn8/vtO/stuIG/RP1s4nuX/5AKrkSSL4JkdmP42WPbaKH0SWRK3nkn8+/srnZShTUU/18oo+iiyJX8sg35n+p+WTmrZe0u7ZVfwq6LHIljvzz+W3XXitx5A849ugj6MLIlTjy27DTnDzBj070Y3Rp5EoaecRGrdfSit1XPBN9gC6OXEkjv18GK9rDyzrZWYqyH0YXR66kkVfmn2pu6QeZejWM/pE0ciWM/P78WyWWWi356kf+JLokciWMPKKC222ZxctXFNVcfQJdFLmSRX6/AdJpn1fNLeHv3Nb9cXRR5EoWOVYFFz46laXcro2iyyBXssgjenAtoHiJPNlr09GFkCtR5PcL2Kn9vDm3v0O1XTtFl0KuRJHfXyVI7RQ5vj4FXQy5EkW+iVu1TxZ0e9kdkipugP5DKeRKEnnE5rzZwoi9/QOCKm4cnZNcSSL/9/lf5KsWUuSXwI9GV+ejs5IrQeT3AQ/IJK7kgRf3I38eOi+5EkQekdrhl3PELH/Tn4POTK4EkUek9mYLP+CyfG02Oje5kkMeldr3WyQBlOWnJvchOju5kkMeldqXWmQBkOWnJ/c++q/YyZUc8ojUrncqSj/Lv2piJPcBOjP5Pygx5FGpHb2Em9KVb8In9z76P/GSzzWnJY9K7TsthjBgn5XcT9D5yOeZE5Pfj/gOey2e0H4GV52Hzkg+x5yavBIwdeHiPIMDTe7DQo6PfLY5Nfn9qBtkllqMoXO6ZtWfi85GPtOcnPx+WVTZPmX/1oNL7n30H7ORzzKnJ4/8FQ5u836KT6i+7s9HZyKfYU5Pfj/yJxl2W/yR7Gqisj8fnYl8ujkD+f3Ii7tbIiLRm2/1+ei/5yGfas5BHtWE49meG95iUPPjodOSTzPnIP8yaqfGtj03uZDqyI+FTkw+xZyF/MuCPebxrxZd9eOgU5NPmvOQfxl5QX+2JRM9/3LvQHNBP0YnJ58wZyKf+ZpatiehJTMbfZB+dvc1F/QB+i/JycfNmci/3Jx9NmZvR575+R3xw67BblNzQR+iU5OPmXOR/3HWcv7y7D5HWeatg1CVkddd0M/R6cjD5mzkX63OOw41RBdmng83B7O6C/opOiF5yJyP/JP5/da8PPNWL1RY7mkv6MfolOSj5nzkX3UjbhJZktBun1LGnf+22472gj5A/ykl+Y+UBPKvclEnXZvizAfKI4/0szot93F0GvJzc07yr8pRj8zz8syzocuGZzXn/AToRORn5qzkd6Ifn/bEmS+FSoy8xjP0MXQq8lNzVvLOZvTj07w48/xBK4b5uh8bnYr8xJyXvFOI8fh0V5r5y/Ark4FJETdE/wUR+bE5M3nnSPQj83hh0JU5RychH5pzk/9Z+CNzE/PAT4JOQj4w5ybvbMh/fKpvXk2C/gcK8r45O3mn4bJ5zddCRyT/keIn75Rczu1Hvg46Jvlsczryzmrgbg0XtxMXRkcln2lOSH4ncNn8pp8cHZd8ljkheafignle/3zUBDoy+QxzSvJOQ/CrKwDmVT8p+g9wyaebk5J3Si6Y7wUghXsIHYl8qjkt+ewSTtw5Ca1D7+u+JjoW+TRzYvI77DdFwTxZBSrcz9DRyKeYE5N3Nqx4jyHmmUizjvsoOh75TxQ3each/r3EGLE7ZxC+JjoW+YQ5OXknJ/qdc+OyPflm7TiuoJGPm9OTd46k3icCU8JpbNYm0GHJx8wZyDu2vKQ2N5r6byRHowOTh805yDdseOncZDnX2ayF0KHJQ+Yc5J1NtivbSToyiZ+sTaCDk4+as5B3GlIvhUsS+/rXykShw5OPmPOQd+bfI3Ngh/kOkrl/BYH83JyJvFOWfkOYaWpP/DR1JjoQ+Zk5F3knEHFRP1bjVb8pM4kORX5qzkZ+N+q6qF355C+NrgyLiw5GfmLORt7ZiLpHxoK2TNRFUlUfAB2O/NicjzyibLeiitvdwTUfogOSD80ZyTuRd4TJ36JHXh1W843RIckH5pzkncgrP+VP9MhbvtdNzf3LkOR9c1byTuS9cOInevS1kObms9C1yH+ieMk70eTCS/fdHQrz6eh65GFzevK7McxlN2Bj/ABf2cdB1yQPmdOTP1uOdW9q3uICDsp8El2X/GeKlfxZN5b5wa7FmR3KfBxdm3zEnIP8WSOWudyzE7F+twHIPIyuT35uzkL+LBfzVmyhbfd4v8EFZT6KbkB+Zs5D/qwU2Iwe82fXVn1wdBPyU3Mm8mflIDa6uDV9N/ZvZvvQ6EbkJ+Zc5M8S/KDNgbDezF78X+PxgdHNyI/N2ciTmPfV98Vs2vL7SX5/yYdFNyQfmvORLwfJ4pVlKzmCuX/ZlHxgzkf+IKl504oDcLjmU9CTkffNGckfvE36E6QvhaT2gNF8Aj0h+c8UJ/mDRlJzIXXcEqv5GHpS8khzVPLk5kIacj1e8xB6YvIoc1zyB4Wk5jJeZtoNmM1H0JOTR5gjkz8oBVYm9z128zN0DfL55tjkGuYierBZfvMTdB3yv1ec5BrmB/bt1HDMh+ha5PPM8ckfBMkjb19qxzHvo+uRzzEnINcxF9CK25dh7l/WI59tTkGuY35gX2rHMp9Aj0c+05yEXMecv3JPnNrRzMfQY5LPMqchv6tjvm9b1Q54ZmIuelzyGeY05IkfsYhoy+wm/sRlnwI9Nvl0cyJyPXPu5L4nyvwMPT75VHMqck1z5p57U5b5CXoC8mnmZOSa5rwPVPOBMPMhehLyKeZ05LrmrNcQvEr+eY98bPRE5JPmhORfa5ofWLU5h3lHcT56IvIJc0pyXXPOKm4vEGg+iT6PfNyclFzbnLGK64k0H0efS/5zxUj+SNecr4rL63zaqk+MPp88bE5Mrm/O9qBlX6r5KHoEecicmlzfnOvqiV2tT1v3SdGjyEfNycn1zbm2a0taH9b3KdEjyUfM6ckNzA+s2ajRmR+jR5OfmzOQG5jzbNf2tD5q2SdEj0F+Zs5BbmLO8hbTgXBz/3Ic8lNzFnITc45zcXrTnGJ7PgN9OvmJOQ+5kTnD7+015ZuH0GeQH5szkT+6FNg00fOaH7Tq86DPIh+ac5E/ehTYNNF7Vpifoc8kH5jzkRuZU0903Wke+D4H+mzyvjkj+aOyRRNdd5qv+hzoc8h/rjjJH5Xsmeh7up+y7DOgzyMfN6cl/yIXWDPRD3Q/5brPgD6PfMycmPyLhpE5ZTNOe5qb/0qDKfo4+RXFSW5qfmDBNKcu2yfQJ8hD5uTkXyybmdM9XlvS/4y+z4o+ST5qTk++dc/QnOo5+u6O9kdc9VnRp5CPmDOQb22VDdGJDszs63/CI58TfRr5uTkL+VbB0JzmZFze4APWfEb0qeRn5jzkW11T857kdgxfCXeCPpX81JyJfOtjU3OK/dqewce76fti0K+EzLnIzRd0gt9qMSjgWLpws9CvhMz5yM0XdPwy7pXJp1v3paBfCZkzkm8tG5tjt93zRh+u6gtBvxIy5yTf2lo1Nsc9Grd7EFi7nI+gXwmZ85IDJHfcbpxRZudezs/Qr4TMmcm3bgWis7tZZmdfzk/Qr0w15yIHqNwxs7tZZqd6bSkSfao5H/lWA2Civ5KZ2fma7XPRFTf51u1AbnbfM/xYR75EdMVNDlLFIZVxL3cMP1bVl4iu2MlBqjgc86XAjdQ+hq7YyT/7rOSq+ZEvEl3xk3+27Kp53ReJrvjJISa6SPNV3xeJrgSQA0x0keY1Xya6EkD+7bclF81v1n2Z6EoC+ePvZZrn3angRtGVBPLHjwsOmtd9oehKBPnj56vOmUuc5sfoSgT548cNiea7zk3zIbqSQf748ZHEk5DuTfMBuhJCbljGIT1jOXBvmvdDCSE3zO5I5j0Hp/m4OSP54ycleeavHNqbzzBnJX9yx6B2R3qFSbspU/MtMeclf/Kkom8u7DRc2bfEnJv8yZOGNPNdBzP7qDk/+ZMnuvfLoB2CPHAvs4+YSyB/8jtNdLS3U7VePF/37TCXQf67O3qtGbTfzd1zbJs2ai6EXBcd7U2Wlw6Sn5iLIf/tb+/obNPxXkJPepfzzZpvh7kg8n7kxLRkkndlynXfDnNZ5E+fdleFbNWS7tDLVd+3w1wa+dOnGwkXdcy7AWPv1srrdogPzOWR96Ob6M3FIuNtgDfL5fX1arXu2xNKJPnTp990c7HZi23MOweyc619C0MJJf/mN4OoVCqNRiNXKs0r5tcOMc1bCyv/E87h1lqPmMsl/82l0bhXOf4v0Cj0/w+UzrPAQhvV/Ho7M0Bftd/63Nwa8n48DMfzTzc3N/+7jWv+Ybud8eq+Q6FsJh/G/yGbt66221mv5pK57eT/2Sdvv8Y0f9Nur3heqe6Uuc3kw2ne/hDTfLH/DxQ9z8s5ZG41+f+20c37C3r7sNlHX6u6Ym41+fP/Gpov4hbu7UEZN4iSG3W73eTdw6H5Nezc3j70PGfUlc3k7wrZdhs9ub8Z/gvFE3RvreauuXzy7pqXOTFfxN2rtYel+1nk6m6aiyevbPe//ZUT86u4JdwgsiPoXqlWd89cOnklN/jqe+02+kRfPPkXMl447GVXVpIfi3te8cz86mvc1H5exdnPruwjf9fdPv3SM230iX797F8oelNiLVd1w1wyeaWwdv6Nr5ybY5Xu187+gYw3I2yb7soq8o3GdujbHiFHyu4fnv8Dh96cKFk035U15O82C9tjX3R21Lz9BnM1n6jcp+Z5Oya8soO80shN+ZKLIXOMJX1x9O9nvBjRn/B128zFkb/bnOo9VsIN4zp4Zh+d5qG2zPwJLxteSSZ/V2lM5PPRWBgzv/ohWgE3jKaXJMTCK6Hkd5fnc0+UcBjoi2N/vugljlKuVpVtLoJ8+W1jdD82O5ptXPRwZo+7oE8t7kRNeSWJfKMSY3LPKtvB0V9fG//jK55ByFnklQzyT5YTaU8t26HR30z+8aZnGhL28Yqb/M5yt1HQ+/4ybUz0xSl/O+tBBPejWMVIfquiMbkjzdtXr8P22QEW9Al1Gea05MvdQsH4m1toz4jroD3XkVgAMvfW6gLMCclvNbZhvriVWebmbdiJkj1Gyz1Z1NnN6cjfboN9be3Zce01Bnm73YNDrzKbk5F/UoD70uaZm1VyM8mBirjjqLGak5G/XQP8znrtubGIQA5WxLHOdEVJ3oD8xqa2ZEKL+mtwcrgijnNNV4TkBY/UXDO/zyM368RJQVdk5He2Yb+v6W24sfz+Gpa83YYdAs+WTZHNcmjyWS0Zs/7M9fnkoEUcV3NGWZrYY5r3d20fGnffDB+niivelbXkMc3bVxeNHqtgFu5MS7oi2qR5bObxC/jJh6cE5gxLuiIhv4VAPrvdrtmVi6jeMDZrPEu6oiC/s8ZrHgv9eqy/dAg/jiqDOX6PveAxm8doyl2P+Zfgx7FGb45P3vXYzSPfbIpLDr1Z48juCp/8kzUB5m+AyDHMqWt3hf+8HCezz3t8Pm3HBkQOX7gPDsnxmsOTI2X2+Y9Sk52d+bDNa05cxils8jtrMswXDTdpeJs18jJOYZ99w8rsSc2vGbVikM1pyziFTL7sCTGffSfsm0R/5hBnMHUmc4wTrgUx5tcTnGOn3aBTT3SFS/7WE2O+aF6/Ab3Lwj3RFe459m055tMX9NdXk5pnrZ/oCpW84ckxn7qgJ6vfMM0JJ7rCJMfbp+mYXwchBz81QT/RFeY7aajTPLH5GxBynKYM6URXiOS40zyx+VUQcjzzHKk50punuNM8sflEcn+jQ47UlKGc6AqPHHmaJzd/Y7YxRzfP0Zmj/eapJ8z8qu6zNCJzqomu8K4U2JZmHjo4EXtjfphBfZWFYaIrNPK3Hp35SiZxK+5a/HmdoWi+Ej5eU2h3xWBPc+/wfCpms0lbcYtJcnmGyJzoDQeFRb6MTT5yNqroZRMm9wQt18z4kRzP9omusG6EKtCZZ2Iv7osa27TBX2+uEDRfyQ7MKCTyTzwy84X4h+Pe6B6G6h3SmOfozeHufWuQmQ9/1jLuIdjXCQu4s/569pDEnGS7ppCu+lsjMz8myCRpxSV6Zn5CXKQxzxGbA5K/9ajMi+MkMZL7NQ3z8/9VRc/yKk7h3OFaIDDPjHbFYhbuw+Se7GhM+N/DfMhCtV1TKOQEFdyxwcrpSaVegucsb/TMT4t3XPMSoTnoTc0NKvNs0k7staTTfKTVelLH4ZoTVHEK5XLuNSLzzLQOTVRbJtnztNFHKkUK8xyVOSx5hYK8L7AyvrrHacskPPWYmSghkM3XiMyBr+AvkJhnQ7umuOZXEz5DDQuv4Jvj9+IUAvkdj8Y8403kXfgI78wG/Ths8xKFOfQPbXRpzL1meNbjRHZ8QcE8NEFTxSmEn9PZ9jiCxrxfK6Kb19DNwcnvspAnu4JA//W0HuZBGZoqTsH/aFaDx3yBxrxfLHqWJ3cF/ztpPKk9/hWBhicem4foQ8mhmwOTb/CQIxVxUxbvTM/y5K7Afw2RKbXH7rgbv7XSbKKPBXeLrsB/AJMptSMV7kWWoeTozY1+85SLHKeIy/KMhdzc6JeNG2zmGYfMq8TmZj9mzZbacbqvTGPJ0ZqbkfOldpTCfYVrMKTmhj9Z3+Uz92i2avYndwVLzpjaUbqvGc/B5K5gye8ykmMU7mzmHpm5Kfmlt5zmGWfKdtzkrkDJLxU4zbMumedozM3JL3GSY3RfGUdDYg5AXmE1H3kj3fatGmpyV5DkvKkdoYhbYBxMDt8cgvzeNq95xp2yHTO5K0jyDV5y+O5rkXM0dWRzEPJ7DWbznjtlO2ZyV4Dk3Kkdvohrcg5mDdUciPwdNzl0EXfIO5o6ojkQ+b237OYZd8p2D++cu4Ijf1hgN886VLbjJXcFR/5wjd286VDZjpfcFRx5xeOPFXfKdrzkrsDIHzYEmGfcKds9tDdUFRj5w20B5kWHyna0VpwCI38vgBy2iFtgH04VyxyG/GFXgrnnUNmO1opTUOQCdmrQXZki+2hwdmsKivyhCHLQIi7LP5w6jjkQ+aYMc8giTsBwUJK7AiIXsVODfbS2IGA4a9jmJuQidmqwXZmMhOHUcc2NyN8LIQdc0IsShlNDNTciF7JTA13QsxKGU8I0NyMXslMD7crIGE8dz9yQ/KEnJqDOyqzIGE4NzdyUfFOO+YJLJRzKbk2BkEvZqUEu6EUh40EyNyYXs1MDXNCzQsZTRTE3J3/vCYpDl0o4jOSuIMjl7NTgFvQFKcNZQzAHIH9ekGSecamEw9itKQjy55LIgRb0opjx1NDMTcg3RZnDLOg9z9nkrgDInzdkmS+405FBSe4KgPz5tizzokslHEJyVwDk72WRgyzoRUHjKWGYm5E/7wozh1jQs5LGg2BuSC5rpwa0oIsaTxXc3JT8+Zo086JTyzl4K06Zk1ekkQMcisuIGg/wbk0Zk0vbqQ1ixanlHHq3pozJX2zLM884tZxDJ3dlTP6pPHLjBX1B2HjWgM0NyV90BZo3nVrOoZO7MiV/URBobrqgZ6WNpwZubkIucTk3XtDFjacEbW5EviyR3LD9uiBvQMDmRuQil3PT9mtG3niqoOZm5C9yMs0zTi3nsLs1ZUj+Qia50W7tUOKAMMw1yTeFmjfdWs5Bd2vKjPxFQ6i5yW6tKHE8OXBzXXKZOzXD5N6TOJ41aHNt8u+lkhvs1lZkDqgOa65NfqMr1lw/uWdkjqcGaq5PfqMg1zzj1HIO2YpTRuQ31uSaay/oTc/x5K6MyJc9wXHo0E4NtBWnTMhvNCSbZ5xK7YC7NWVCfiMn2bzo0E4NtBWnTMg/lUyu2YpbkTugOqi5HvmNTdHmesfcM3LHk4M01ySXvZxrJves3PGsAZrrkt/Y9pxL7oeSB1QHM9cm/97znEvuGcnjqUGZa5NLbrxqJ/ei5PEAteKUPrnkxutxaLzDJHtAdXjzZOSSG6+6z1kWZI+nBm6ekHxZPHny5F6UPZ4ctHlCcvnLuUZybwofELB5UnLZjVe95L4gfTxVUPPE5DcsIE+a3IvSx5ODNE9OXrHBvOlWaodpxSldcumNV522zIL88dTBzDXIP9j2nEvuRfnjqUGZ65C/t4I8UXI/tGA8JSBzHfIP3tphnuS0TMaG8dRBzLXIP2pYYl50KrWDJHelSf7RtiXm8Y9CHloxHIDdmtIk37CFPH5yz9gxHhhzDfKPutaYZx04IQPbilN65B8VrDGP239dsWQ4OQBzLXJ7lvPYVVzRkuGYt+KUHrk9y3nsh2s9W8ZTBzaPSW7Rch63/7pgzXBqsOZxyW1azmMm96I1wymBmscmt2k5j7dFP7RoOKbJ/W8CDACkQTUg3C54LgAAAABJRU5ErkJggg==',
        'logo1' => 'https://image.ibb.co/k92AFQ/h3k_logo_dark.png',
        'logo' => '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',
        'sprites' => 'iVBORw0KGgoAAAANSUhEUgAAAYAAAAAgCAMAAAAscl/XAAAC/VBMVEUAAABUfn4KKipIcXFSeXsx
VlZSUlNAZ2c4Xl4lSUkRDg7w8O/d3d3LhwAWFhYXODgMLCx8fHw9PT2TtdOOAACMXgE8lt+dmpq+
fgABS3RUpN+VUycuh9IgeMJUe4C5dUI6meKkAQEKCgoMWp5qtusJmxSUPgKudAAXCghQMieMAgIU
abNSUlJLe70VAQEsh85oaGjBEhIBOGxfAoyUbUQAkw8gui4LBgbOiFPHx8cZX6PMS1OqFha/MjIK
VKFGBABSAXovGAkrg86xAgIoS5Y7c6Nf7W1Hz1NmAQB3Hgx8fHyiTAAwp+eTz/JdDAJ0JwAAlxCQ
UAAvmeRiYp6ysrmIAABJr/ErmiKmcsATpRyfEBAOdQgOXahyAAAecr1JCwHMiABgfK92doQGBgZG
AGkqKiw0ldYuTHCYsF86gB05UlJmQSlra2tVWED////8/f3t9fX5/Pzi8/Px9vb2+/v0+fnn8vLf
7OzZ6enV5+eTpKTo6Oj6/v765Z/U5eX4+Pjx+Pjv0ojWBASxw8O8vL52dnfR19CvAADR3PHr6+vi
4uPDx8v/866nZDO7iNT335jtzIL+7aj86aTIztXDw8X13JOlpKJoaHDJAACltratrq3lAgKfAADb
4vb76N2au9by2I9gYGVIRkhNTE90wfXq2sh8gL8QMZ3pyn27AADr+uu1traNiIh2olTTshifodQ4
ZM663PH97+YeRq2GqmRjmkGjnEDnfjLVVg6W4f7s6/p/0fr98+5UVF6wz+SjxNsmVb5RUVWMrc7d
zrrIpWI8PD3pkwhCltZFYbNZja82wPv05NPRdXzhvna4uFdIiibPegGQXankxyxe0P7PnOhTkDGA
gBrbhgR9fX9bW1u8nRFamcgvVrACJIvlXV06nvtdgON4mdn3og7AagBTufkucO7snJz4b28XEhIT
sflynsLEvIk55kr866aewo2YuYDrnFffOTk6Li6hgAn3y8XkusCHZQbt0NP571lqRDZyMw96lZXE
s6qcrMmJaTmVdRW2AAAAbnRSTlMAZodsJHZocHN7hP77gnaCZWdx/ki+RfqOd/7+zc9N/szMZlf8
z8yeQybOzlv+tP5q/qKRbk78i/vZmf798s3MojiYjTj+/vqKbFc2/vvMzJiPXPzbs4z9++bj1XbN
uJxhyMBWwJbp28C9tJ6L1xTnMfMAAA79SURBVGje7Jn5b8thHMcfzLDWULXq2upqHT2kbrVSrJYx
NzHmviWOrCudqxhbNdZqHauKJTZHm0j0ByYkVBCTiC1+EH6YRBY/EJnjD3D84PMc3++39Z1rjp+8
Kn189rT5Pt/363k+3YHEDOrCSKP16t48q8U1IysLAUKZk1obLBYDKjAUoB8ziLv4vyQLQD+Lcf4Q
jvno90kfDaQTRhcioIv7QPk2oJqF0PsIT29RzQdOEhfKG6QW8lcoLIYxjWPQD2GXr/63BhYsWrQA
fYc0JSaNxa8dH4zUEYag32f009DTkNTnC4WkpcRAl4ryHTt37d5/ugxCIIEfZ0Dg4poFThIXygSp
hfybmhSWLS0dCpDrdFMRZubUkmJ2+d344qIU8sayN8iFQaBgMDy+FWA/wjelOmbrHUKVtQgxFqFc
JeE2RpmLEIlfFazzer3hcOAPCQiFasNheAo9HQ1f6FZRTgzs2bOnFwn8+AnG8d6impClTkSjCXWW
kH80GmUGWP6A4kKkQwG616/tOhin6kii3dzl5YHqT58+bf5KQdq8IjCAg3+tk3NDCoPZC2fQuGcI
7+8nKQMk/b41r048UKOk48zln4MgesydOw0NDbeVCA2B+FVaEIDz/0MCSkOlAa+3tDRQSgW4t1MD
+7d1Q8DA9/sY7weKapZ/Qp+tzwYDtLyRiOrBANQ0/3hTMBIJNsXPb0GM5ANfrLO3telmTrWXGBG7
fHVHbWjetKKiPCJsAkQv17VNaANv6zJTWAcvmCEtI0hnII4RLsIIBIjmHStXaqKzNCtXOvj+STxl
OXKwgDuEBuAOEQDxgwDIv85bCwKMw6B5DzOyoVMCHpc+Dnu9gUD4MSeAGWACTnCBnxgorgGHRqPR
Z8OTg5ZqtRoEwLODy79JdfiwqgkMGBAlJ4caYK3HNGGCHedPBLgqtld30IbmLZk2jTsB9jadboJ9
Aj4BMqlAXCqV4e3udGH8zn6CgMrtQCUIoPMEbj5Xk3jS3N78UpPL7R81kJOTHdU7QACff/9kAbD/
IxHvEGTcmi/1+/NlMjJsNXZKAAcIoAkwA0zAvqOMfQNFNcOsf2BGAppotl6D+P0fi6nOnFHFYk1x
CzOgvqEGA4ICk91uQpQee90V1W58fdYDx0Ls+JnmTwy02e32iRNJB5L5X7y4/Pzq1buXX/lb/X4Z
SRtTo4C8uf6/Nez11dRI0pkNCswzA+Yn7e3NZi5/aKcYaKPqLBDw5iHPKGUutCAQoKqri0QizsgW
lJ6/1mqNK4C41bo2P72TnwEMEEASYAa29SCBHz1J2fdo4ExRTbHl5NiSBWQ/yGYCLBnFLbFY8PPn
YCzWUpxhYS9IJDSIx1iydKJpKTPQ0+lyV9MuCEcQJw+tH57Hjcubhyhy00TAJEdAuocX4Gn1eNJJ
wHG/xB+PQ8BC/6/0ejw1nAAJAeZ5A83tNH+kuaHHZD8A1MsRUvZ/c0WgPwhQBbGAiAQz2CjzZSJr
GOxKw1aU6ZOhX2ZK6GYZ42ZoChbgdDED5UzAWcLRR4+cA0U1ZfmiRcuRgJkIYIwBARThuyDzE7hf
nulLR5qKS5aWMAFOV7WrghjAAvKKpoEByH8J5C8WMELCC5AckkhGYCeS1lZfa6uf2/AuoM51yePB
DYrM18AD/sE8Z2DSJLaeLHNCr385C9iowbekfHOvQWBN4dzxXhUIuIRPgD+yCskWrs3MOETIyFy7
sFMC9roYe0EA2YLMwIGeCBh68iDh5P2TFUOhzhs3LammFC5YUIgEVmY/mKVJ4wTUx2JvP358G4vV
8wLo/TKKl45cWgwaTNNx1b3M6TwNh5DuANJ7xk37Kv+RBDCAtzMvoPJUZSUVID116pTUw3ecyPZI
vHIzfEQXMAEeAszzpKUhoR81m4GVNnJHyocN/Xnu2NLmaj/CEVBdqvX5FArvXGTYoAhIaxUb2GDo
jAD3doabCeAMVFABZ6mAs/fP7sCBLykal1KjYemMYYhh2zgrWUBLi2r8eFVLiyDAlpS/ccXIkSXk
IJTIiYAy52l8COkOoAZE+ZtMzEA/p8ApJ/lcldX4fc98fn8Nt+Fhd/Lbnc4DdF68fjgNzZMQhQkQ
UKK52mAQC/D5fHVe6VyEDBlWqzXDwAbUGQEHdjAOgACcAGegojsRcPAY4eD9g7uGonl5S4oWL77G
17D+fF/AewmzkDNQaG5v1+SmCtASAWKgAVWtKKD/w0egD/TC005igO2AsctAQB6/RU1VVVUmuZwM
CM3oJ2CB7+1xwPkeQj4TUOM5x/o/IJoXrR8MJAkY9ab/PZ41uZwAr88nBUDA7wICyncyypkAzoCb
CbhIgMCbh6K8d5jFfA3346qUePywmtrDfAdcrmmfZeMENNbXq7Taj/X1Hf8qYk7VxOlcMwIRfbt2
7bq5jBqAHUANLFlmRBzyFVUr5NyQgoUdqcGZhMFGmrfUA5D+L57vcP25thQBArZCIkCl/eCF/IE5
6PdZHzqwjXEgtB6+0KuMM+DuRQQcowKO3T/WjE/A4ndwAmhNBXjq4q1wyluLamWIN2Aebl4uCAhq
x2u/JUA+Z46Ri4aeBLYHYAEggBooSHmDXBgE1lnggcQU0LgLUMekrl+EclQSSgQCVFrVnFWTKav+
xAlY35Vn/RTSA4gB517X3j4IGMC1oOsHB8yEetm7xSl15kL4TVIAfjDxKjIRT6Ft0iQb3da3GhuD
QGPjrWL0E7AlsAX8ZUTr/xFzIP7pRvQ36SsI6Yvr+QN45uN607JlKbUhg8eAOgB2S4bFarVk/PyG
6Sss4O/y4/WL7+avxS/+e8D/+ku31tKbRBSFXSg+6iOpMRiiLrQ7JUQ3vhIXKks36h/QhY+FIFJ8
pEkx7QwdxYUJjRC1mAEF0aK2WEActVVpUbE2mBYp1VofaGyibW19LDSeOxdm7jCDNI0rv0lIvp7v
nnPnHKaQ+zHV/sxcPlPZT5Hrp69SEVg1vdgP+C/58cOT00+5P2pKreynyPWr1s+Ff4EOOzpctTt2
rir2A/bdxPhSghfrt9TxcCVlcWU+r5NH+ukk9fu6MYZL1NtwA9De3n6/dD4GA/N1EYwRxXzl+7NL
i/FJUo9y0Mp+inw/Kgp9BwZz5wxArV5e7AfcNGDcLMGL9XXnEOpcAVlcmXe+QYAJTFLfbcDoLlGv
/QaeQKiwfusuH8BB5EMnfYcKPGLAiCjmK98frQFDK9kvNZdW9lPk96cySKAq9gOCxmBw7hd4LcGl
enQDBsOoAW5AFlfkMICnhqdvDJ3pSerDRje8/93GMM9xwwznhHowAINhCA0gz5f5MOxiviYG8K4F
XoBHjO6RkdNuY4TI9wFuoZBPFfd6vR6EOAIaQHV9vaO+sJ8Ek7gAF5OQ7JeqoJX9FPn9qYwSqIr9
gGB10BYMfqkOluBIr6Y7AHQz4q4667k6q8sVIOI4n5zjARjfGDtH0j1E/FoepP4dg+Nha/fwk+Fu
axj0uN650e+vxHqhG6YbptcmbSjPd13H8In5TRaU7+Ix4GgAI5Fx7qkxIuY7N54T86m89mba6WTZ
Do/H2+HhB3Cstra2sP9EdSIGV3VCcn+Umlb2U+T9UJmsBEyqYj+gzWJrg8vSVoIjPW3vWLjQY6fx
DXDcKOcKNBBxyFdTQ3KmSqOpauF5upPjuE4u3UPEhQGI66FhR4/iAYQfwGUNgx7Xq3v1anxUqBdq
j8WG7mlD/jzfcf0jf+0Q8s9saoJnYFBzkWHgrC9qjUS58RFrVMw3ynE5IZ/Km2lsZtmMF9p/544X
DcAEDwDAXo/iA5bEXd9dn2VAcr/qWlrZT5H7LSqrmYBVxfsBc5trTjbbeD+g7crNNuj4lTZYocSR
nqa99+97aBrxgKvV5WoNNDTgeMFfSCYJzmi2ATQtiKfTrZ2t6daeHiLeD81PpVLXiPVmaBgfD1eE
hy8Nwyvocb1X7tx4a7JQz98eg/8/sYQ/z3cXngDJfizm94feHzqMBsBFotFohIsK+Vw5t0vcv8pD
0SzVjPvPdixH648eO1YLmIviUMp33Xc9FpLkp2i1sp8i91sqzRUEzJUgMNbQdrPZTtceBEHvlc+f
P/f2XumFFUoc6Z2Nnvu/4o1OxBsC7kAgl2s4T8RN1RPJ5ITIP22rulXVsi2LeE/aja6et4T+Zxja
/yOVEtfzDePjfRW2cF/YVtGH9LhebuPqBqGeP9QUCjVd97/M82U7fAg77EL+WU0Igy2DDDMLDeBS
JBq5xEWFfDl3MiDmq/R0wNvfy7efdd5BAzDWow8Bh6OerxdLDDgGHDE/eb9oAsp+itxvqaw4QaCi
Eh1HXz2DFGfOHp+FGo7RCyuUONI7nZ7MWNzpRLwhj/NE3GRKfp9Iilyv0XVpuqr0iPfk8ZbQj/2E
/v/4kQIu+BODhwYhjgaAN9oHeqV6L/0YLwv5tu7dAXCYJfthtg22tPA8yrUicFHlfDCATKYD+o/a
74QBoPVHjuJnAOIwAAy/JD9Fk37K/auif0L6LRc38IfjNQRO8AOoYRthhuxJCyTY/wwjaKZpCS/4
BaBnG+NDQ/FGFvEt5zGSRNz4fSPgu8D1XTqdblCnR3zxW4yHhP7j2M/fT09dTgnr8w1DfFEfRhj0
SvXWvMTwYa7gb8yA97/unQ59F5oBJnsUI6KcDz0B0H/+7S8MwG6DR8Bhd6D4Jj9GQlqPogk/JZs9
K/gn5H40e7aL7oToUYAfYMvUnMw40Gkw4Q80O6XcLMRZFgYwxrKl4saJjabqjRMCf6QDdOkeldJ/
BfSnrvWLcWgYxGX6KfPswEKLZVL6yrgXvv6g9uMBoDic3B/9e36KLvDNS7TZ7K3sGdE/wfoqDQD9
NGG+9AmYL/MDRM5iLo9nqDEYAJWRx5U5o+3SaHRaplS8H+Faf78Yh4bJ8k2Vz24qgJldXj8/DkCf
wDy8fH/sdpujTD2KxhxM/ueA249E/wTru/Dfl05bPkeC5TI/QOAvbJjL47TnI8BDy+KlOJPV6bJM
yfg3wNf+r99KxafOibNu5IQvKKsv2x9lTtEFvmGlXq9/rFeL/gnWD2kB6KcwcpB+wP/IyeP2svqp
9oeiCT9Fr1cL/gmp125aUc4P+B85iX+qJ/la0k/Ze0D0T0j93jXTpv0BYUGhQhdSooYAAAAASUVO
RK5CYII=',
    );
}