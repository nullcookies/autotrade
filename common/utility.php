<?php
if (!defined('IS_VALID')) die('Access denied.' . "\n");

class Utility
{
    public static function writeLog($string = null)
    {
        try {
            $log_file = LOGS_PATH . DS . date('Ymd') . '-log.txt';
            $str      = '';
            $str .= date('Y-m-d H:i:s') . "\t" . self::getClientIp();
            $str .= "\r\n" . $string . "\r\n";
            $str = strval($str);
            $fp  = fopen($log_file, 'a');
            fwrite($fp, $str);
            return fclose($fp);
        }
        catch (Exception $e) {
        }
    }
    
    public static function getClientIp($checkProxy = true)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = @$_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
    
    public static function returnJson($arr = array())
    {
        header('Content-Type: application/json');
        die(json_encode($arr));
    }
    
    public static function redirect($url = '/', $time = 0, $exit = true)
    {
        if ($exit)
            die('<meta http-equiv="refresh" content="' . $time . ';url=' . $url . '"/>');
        else
            echo '<meta http-equiv="refresh" content="' . $time . ';url=' . $url . '"/>';
    }
    
    /**
     * Makes directory, returns TRUE if exists or made
     *
     * @param string $dir The directory path.
     * @return boolean returns TRUE if exists or made or FALSE on failure.
     */
    public static function mkdir_recursive($dir = null, $mode = 0644)
    {
        if ($mode && is_dir($dir) and shell_exec('which chmod'))
            @chmod($dir, $mode);
        is_dir(dirname($dir)) || \Utility::mkdir_recursive(dirname($dir), $mode);
        return is_dir($dir) || @mkdir($dir, $mode);
    }

    /**
     * Makes directory, returns TRUE if exists or made
     *
     * @param string $dir The directory path.
     * @return boolean returns TRUE if exists or made or FALSE on failure.
     */
    public static function rmdir_recursive($dir = null)
    { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($dir . "/" . $object))
                        rmdir_recursive($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object); 
                } 
            }
            rmdir($dir); 
        } 
    }

    // ------------------------------------------------------------ //
    
    public static function func_read_config($file = null)
    {
        if (is_file($file) and file_exists($file))
            defined('CONFIG_FILE') or define('CONFIG_FILE', $file);
        
        if (!is_file(CONFIG_FILE) or !file_exists(CONFIG_FILE))
            return array();

        $arr = parse_ini_file(CONFIG_FILE);
        return $arr;
    }

    public static function func_check_login($user = null, $pass = null)
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

    public static function func_replace_by_star($str = null)
    {
        $str_new = '';
        for ($i=0; $i < strlen($str) - 1; $i++) { 
            $str_new .= '*';
        }
        return $str_new;
    }

    public static function func_print_arr_to_table($arr = null, $title = '', $extra = null) 
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

    public static function func_cli_print_arr($arr = null, $title = '', $extra = null) 
    {
        if (!$arr) {echo 'No data found!'; exit;}
        $max_key_length = \Utility::func_max_key_length($arr);
        $max_value_length = \Utility::func_max_value_length($arr);

        echo \Utility::func_fill_space(' ', $max_key_length + $max_value_length + 5, '-') . "\n";
        foreach ($arr as $key => $value) {
            if (is_object($value)) {
                $max_value_length = strlen(serialize($value));
                echo '| ' . \Utility::func_fill_space($key, $max_key_length) . '| ' . serialize($value) . ' |' . "\n";
            }
            else {
                if (strpos($value, '▲') !== false or strpos($value, '▼') !== false)
                    $value .= '   ';
                if ((strpos($value, '▲') !== false or strpos($value, '▼') !== false) and strpos($value, '%') === false)
                    $value .= ' ';
                if ((strpos($value, '▲') !== false or strpos($value, '▼') !== false) and strpos($value, '.') === false and strpos($value, '%') === false)
                    $value .= ' ';
                
                echo '| ' . \Utility::func_fill_space($key, $max_key_length) . '| ' . \Utility::func_fill_space($value, $max_value_length) . ' |' . "\n";
            }
        }
        echo \Utility::func_fill_space(' ', $max_key_length + $max_value_length + 5, '-') . "\n";
    }

    public static function func_max_key_length($arr = null) 
    {
        if (!$arr) return 0;
        $max = 0;
        foreach ($arr as $key => $value) {
            if (strlen($key) > $max)
                $max = strlen($key);
        }
        return $max + 1;
    }

    public static function func_max_value_length($arr = null) 
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

    public static function func_fill_space($str = null, $length = 0, $replace_with = ' ')
    {
        if (!$str or !$length) return $str;
        $str_new = $str;
        for ($i=0; $i < ($length - strlen($str)); $i++) { 
            $str_new .= $replace_with;
        }
        return $str_new;
    }

    public static function func_clean_path($path)
    {
        $path = trim($path);
        $path = trim($path, '\\/');
        $path = str_replace(array('../', '..\\'), '', $path);
        if ($path == '..') {
            $path = '';
        }
        return str_replace('\\', '/', $path);
    }

    public static function func_get_client_ip($checkProxy = true)
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

    public static function func_write_log($string = null, $file = null)
    {
        try {
            $log_file = $file ?: LOGS_DIR . DS . date("Ymd") . "-log.txt";
            $str      = '';
            $str .= date('Y-m-d H:i:s') . "\t" . self::func_get_client_ip();
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
    
    public static function func_redirect($url = '/', $time = 0)
    {
        echo '<meta http-equiv="refresh" content="' . (int) $time . ';url=' . (string) trim($url) . '"/>';
        exit;
    }

    /**
     * List of query parameters that get automatically dropped when rebuilding
     * the current URL.
     */
    public static $DROP_QUERY_PARAMS = array('code', 'state', 'signed_request');

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
    public static function func_should_retain_param($param)
    {
        foreach (self::$DROP_QUERY_PARAMS as $drop_query_param) {
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
    public static function func_get_current_url()
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
                if (self::func_should_retain_param($param)) {
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

    public static function func_show_image($img)
    {
        $modified_time = gmdate('D, d M Y 00:00:00') . ' GMT';
        $expires_time = gmdate('D, d M Y 00:00:00', strtotime('+1 day')) . ' GMT';

        $img = trim($img);
        $images = self::func_get_images();
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
    public static function func_get_images()
    {
        return array(
            'favicon' => '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',
            'logo' => 'iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAA3lBMVEWg6//////C1kqkuDDZQAAQMD8sWHGh7f+ktyilxnCb6v+S2OuU3PAfSmUAFykhTGaLz+O036LD1UGe7//E1Til6OXbMwCkzZK3srTdKwCj6fGi5/qk6OuktRWj4til3uyzvL+78P++pKXk+f+m8/8/boUsT14gRlru+//M9P/Y9v+ywcnc9/+c9P+u7v+/8f+twTiuydG6q6zMcl2n2umxwzC3zEEADyTF1CUPNUvPZ0w2ZHy/mpQhQlDLdmPTVzKr0NymxWSk1a7fGwDHg3XXRRDVTyTSX0PTVjDAnJlnp8P5AAAJtklEQVR4nO3de3vTNhQHYGXN5mLYGAQCoWWtZ7bZqetQWsplF8plbPv+X2iK4yS+yPI50pF8zMPvH/qUtPX7SNbNF4mJ+yRxtkxXUS4j1ll/Ea3SZRYnHv66cPrb4+UqF+Emop7td/PVMnZ6DK6ESZzmKlg7xafy1Fl5uhAm2UpAbA2nWGUulOTCTdmhdBWlLEvqA6IVZpGprqKMMtJjIhTGkaVur4wIS5JKmKSChlciRUp1TtIIlzlR8VWMYb4kOTYKYUrO2yJTgqOzFiYrR76NcWVdWS2FCVXr0m2MLI1WQulzyiuRdkYLoR+ftdFc6Kp9URIt2hxT4ZK0+wMYhWnfYSZMPPs2RrOqaiRc+fcVxpUnYTxAAZZEYTBexQsHKsDSiC9GrHCIM7BGRJ+NSGE6rK8wIjsOnDAfHiiJuTNhPLRtF0yDgxAyqKHbYGoqXMiihm4TRvRCVkDMyQgUJkOLFAF2GzBhzKsANwlh7Q1IuOQIlETQdAMiZAoEEgFCRr1EM5Beo1/IGAgi9gpZAyHEPiFzIIDYI2TbyOzT19zohRl/oCTqr8ZphSw7+nb0Xb9OmIwDKIm6AZxOOPSBI2ImzIc74Bn2BzQzjW6ht6sSrcwuX8yQRs18sVM4YEd4frl4d3EeoH6mu1vsEg7ZjJ5fTqeLv05wxs4GtUs4ZDO6Fk7n8z9wVTXECQdsZUqhNE5PUcXY0dqohW5PwmA2C3QphbKqvr1CGDtORaXQ7UkYHF1c3Gxytc3J1ckuR8FWKI2fjuBVVX0qKoUOeTLBzcdFPfN6ppXMpy/O4Uao0HFPGNwspojM/wX3HMpeUSF03VHMLlBCTM+hqqcKoVufgRDTc0CEzq+Azi7m/aiW8QJUioorqC2h+ymTgXA+fQz85e2JVEvoFFcELZSVNDBvT5vf8LAwMzvDCXFD1NayTVPoULZNRThv9YWtDhE/zdALfcyZ9sL52UWZs01Oi5y9q56A6KliqhN6WZnZCxcnM1XO3+6K+A/8dL/Z2NSFXub1s9O9UFn/Zv+YnIC7NEY2NaGfxTWgcAEfrTVSL8SaMLI6cmhAQnkCIkbcjURdQk/ro3vhvEtodgLuUivEqtBPEQKEny+NTsB9IrXQ1xJ3rzC4sfPVC7Ei9LVAOnvcIxSWvnpzWhH6Wl7rF9onVAm9LQF7EaYKobcV0orwyJWwUog7ob+rvX6Ey5bQ3xrwXjitCIMnoID/St4UerwaOnuhEj58/QAScKHvOoyt0OP96R3CB99AAheuGkKKQw+Uc6H25MiLcDcTLv+luOkiOHrxGJRPUx/C7S0apZBiSBqcfFQtSegWKZyWYVQTUpyFwQl2pXcauBSGVSHJnUHshFlFSDJvwgv/Fi6FZTUVdCM2dsJwL6S52mQgrByvC2G8E9J09/yE6U5IMybFCz9X1mIcCDdjU0E3Jq0I5wtQpo6FxdhU0E2c9sL5qTgCxW0t3UyhBFVfURfqbyfZpfLTLoRFfyGIRt1NIfqnnQjFRkg1NWQoXJ+Igu5mbo7CrBBSLbJxFKaFkGqFhqFw3SMKumVEjsJwLSRbg2IpTKSQ7CYvlsJYCslW81kKUykku2rIUShHNYJusZulMJdC9LF0haVQ+uiuObEUhhNBd8FCIYTNMAKXwkTQ3RHcFgb37gMO+v69wKEwFnQPUfIUZsKwO1RdhWEpTA2FwdXP7fw3Zyk0W0mcnamuwkwZClfCbEijv8+XkVD6zIY04xHmX4VfhSMQmmUg4fVzZ7cYcRBePz84OPAoVD4oUWZBLyx4PoV9swZBKtzyvApBIRFWeF+gsK4zEpr2FrDYCdu8L0qo5H05wi6emdDlQxZGwutOnZEwMpwfwoIWagrPUCjnhy5v0ccJ+3kmQtNVDFgQwuCnpwCfkdDlPfochBnhemk7HIQxfs07OAaFizDBX7c4Bj028DqACq9fPnIpNLj2dAyajD+ACded36FLoTC4fkgm3PYOToW5wTVgGmGl83MqjAyu4xMI6327S2FxHR/bXdgKW0MXp8LY4H4aG6FyYOZUmBjcE2Us7Bp3OhWa3NdmJtQMq10Kc5N7Ew2E+lmDQ2F5byLyOjdW2DsncinMTO4RRglfAg7apTAxuc8bI3x0OKxQGN2rPyZhZPS8BUR4ffCUg3D3vAXuROwTbhpOHsLE6LknrXDXL7AQ7p57wj271i2sdnschJVn11CD7w5ho1dnIdw/f4gamqqE7V6dhdDwOeCmUD0k4yCsPgeMGbjVhJ0jTgbC2rPcmGq6F+oG1ByEpu9UKIU9VxkYCOvvVEBUUykEXEMZXth4LwZi9H0MmS8wEDbebQLv9INj0KEMLmy9nwY8Nh2NsPmOIfDYVAoPASmFkM8+LYWQX3sIFrbeEwWfQj15fwuQ94XwA+Sjtz6shQ9Bv/YWVKh41xe8S0Q8iI54ogTxUZBw0hay338ME+U79wbdloQ6yvcmDrg5EHmqL5/Hvb8UeLI4T69Q/f7S/sHpHYLc/pEgfcRoohb2FeKz37+3zt03P1jn1Z/P4EWIehf0s1++s89v39rn1x5htQhR7/Mei1DzPu+e5nQkQt072XsKcSxC3Xv19QObcQj1eyPoZ8LjEPbsb6GdYoxC2LtHia4QRyHs3WdG19iMQQjYK0izYjMCIWS/J009HYEQtGdX95Uo/kLgvmudIxv2QujeeZ31VM4t7lrnzSuHcwslRvXNrnpKMT+8c5sg6vkhYg/LzsHb0HP7bdRAxD6kA+8laxjUXrKjXHjrkHDc09ko6D2dx7ZAjN+Xe2TLpyZ7q4+rteloZXqEPnZ7pIpOofk/jzuz2KU9ZQIKx9Kgdjaj/UKPWyRZpLVugRGOgdgD7BPy7xa7O0KgkDuxF9gv5E3sBwKEnIkAIETIt7npa2TAQrL30hNne28egZBn16/v6JHCSTI0RxHdUA0vlDMNXsUYamYThkJeRDgQIeTUa0B6CQMhn/YG2MbghUxqKqKG4oUcaiqmhhoIJ7EY1hgKTA01EXrdk1UBbF8BpRcOWIz4AjQTDnY2Ys9AC+EkGaBRDXPgMI1EKGdUnqtqKCAzJUrhuqr6M4ZmFdRSOEm8XdkII7MKaiv0ZbTyWQoLo1tkaOmzFjo22vsIhNLoqs2xaV/2IRDKLHNyZBjmpv1DPTTCdUGSdpChSK2rZxkqoUxGdUbKsw+0TggLoXCyQdopQ1rehFooE6fm56Q891KD2YM+5EKZZBkJbFmuPx8tqc69alwI10mydVlCnMWn8jRzoVvHlbBIEqerfENoUbffzVdp7ApXxKmwTBJny3QV5TIFbv1FtEqXmVtamf8Bri/dKWQ/KqoAAAAASUVORK5CYII=',
            'sprites' => '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
RK5CYII=',
        );
    }
}

if (!function_exists("arr")) {
    function arr($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}

if (!function_exists("dump")) {
    function dump($arr)
    {
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
    }
}

if (!function_exists("debug")) {
    function debug($arr)
    {
        echo '<pre>';
        var_dump($arr);
        echo '</pre>';
        die;
    }
}