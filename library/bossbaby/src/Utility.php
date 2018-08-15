<?php
namespace BossBaby;

class Utility
{
    public static function writeLog($string = null, $file = null)
    {
        try {
            $log_file = $file ?: LOGS_DIR . DS . "debug-" . date("Ymd") . ".txt";
            $str      = '';
            $str .= date('Y-m-d H:i:s') . "\t" . self::getClientIp();
            $str .= "\t" . $string . "\r\n";
            $str = strval($str);
            $fp  = fopen($log_file, 'a');
            fwrite($fp, $str);

            @chmod($log_file, 0777);
            // @chown($log_file, 'dosuser02');
            
            return fclose($fp);
        }
        catch (Exception $e) {
            return true;
        }
    }
    
    public static function getClientIp($checkProxy = true)
    {
        if ( php_sapi_name() == 'cli' ) return 'CLI';
        
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
        is_dir(dirname($dir)) || \BossBaby\Utility::mkdir_recursive(dirname($dir), $mode);
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
                        \BossBaby\Utility::rmdir_recursive($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object); 
                } 
            }
            rmdir($dir); 
        } 
    }

    public static function array_to_object($array = null)
    {
        if (!$array) return null;
        return json_decode(json_encode($array), FALSE);
    }

    public static function object_to_array($object = null)
    {
        if (!$object) return null;
        if (is_array($object) || is_object($object))
        {
            $result = [];
            foreach ($object as $key => $value)
            {
                $result[$key] = self::object_to_array($value);
            }
            return $result;
        }
        return $object;
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

    public static function func_cli_print_arr($arr = null, $title = '', $extra = null, $echo = true) 
    {
        if (!$arr) {echo 'No data found!'; exit;}
        $max_key_length = \BossBaby\Utility::func_max_key_length($arr);
        $max_value_length = \BossBaby\Utility::func_max_value_length($arr);

        $text = \BossBaby\Utility::func_fill_space(' ', $max_key_length + $max_value_length + 5, '-') . "\n";
        foreach ($arr as $key => $value) {
            if (is_object($value)) {
                $max_value_length = strlen(serialize($value));
                $text .= '| ' . \BossBaby\Utility::func_fill_space($key, $max_key_length) . '| ' . serialize($value) . ' |' . "\n";
            }
            else {
                if (strpos($value, '▲') !== false or strpos($value, '▼') !== false)
                    $value .= '   ';
                if ((strpos($value, '▲') !== false or strpos($value, '▼') !== false) and strpos($value, '%') === false)
                    $value .= ' ';
                if ((strpos($value, '▲') !== false or strpos($value, '▼') !== false) and strpos($value, '.') === false and strpos($value, '%') === false)
                    $value .= ' ';
                
                $text .= '| ' . \BossBaby\Utility::func_fill_space($key, $max_key_length) . '| ' . \BossBaby\Utility::func_fill_space($value, $max_value_length) . ' |' . "\n";
            }
        }
        $text .= \BossBaby\Utility::func_fill_space(' ', $max_key_length + $max_value_length + 5, '-') . "\n";

        if ($echo) echo $text;
        else return $text;
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

    public static function func_clean_double_space($string = '')
    {
        $string = str_replace('  ', ' ', $string);
        return $string;
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
            $retained_params = [];
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

    public static function list_file_in_directory($directory = null)
    {
        if (!$directory or !is_dir($directory)) return null;
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        return $scanned_directory;
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
            'favicon' => 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAlwSFlzAAAN1wAADdcBQiibeAAAActpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhNUCBDb3JlIDUuNC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIgogICAgICAgICAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyI+CiAgICAgICAgIDx4bXA6Q3JlYXRvclRvb2w+d3d3Lmlua3NjYXBlLm9yZzwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KGMtVWAAAAxtJREFUOBFVUz1sXEUQ/nZ239vz3bvEwAEpYmSUIhgZlOICwvwIG0RBgRQoEaWx6alSWYqooKBBOssNDSUg0VAgHCsQIxKLBPmIsITRHYoJ/jf4fL73szPMSyjIk1a7O2++mfm+nTEov7k50sXlcfzlN5rCbloMJhwwatSWAx0jWDZULLSXvlop/RREutjcB37l7ZYxMkNkYYXxZyFI1fVxZ1AYgnCAiJlvf/vZ7L0Yc1QmuPuNPzu1OETZJAcOauUdtvRifUAxMb75u8qPkv4QaGyyx8Fdbl9bmiqBWqWW3XyhRWOvTV5/eGyALPWJge1pca9WA6ok6Pac3Sex/wgEsR88tbc+Oc55q71yddY88/o7ze3t7etjTzfDpYvvk4+sYREYrS2oEKwYpwswatO7iHzw0Sd85btFe+axkfOuSPvTIarBH//Fp0+kNqnXkGeFOitn1cHpbilCGjK1CYoimKTY5CNbtWn/cFqZYSKEAtYPUREIu3f6KNhikAUYibDe38Knt79GUFuGmgbSnYm8YfXDBGm1o5Y1Mgq6tfobFj++gYP9A0RJglQr6HS62Ns4wI3VVbg7X6Kz9iO6uwN6yCsd5lFXsqt4i4O9Po6KI5y+UMfP67/j/M1r2DQWbvgUJuNzOMz72Nr4Bbk0kVQ8bmt6HwPlO3ZYBYqd5SG1uMjgZK2G/e4fQJrDVxyO+RiRc+hGF5DaBrz0mVWXEutU2GUiGmdjtU2sDWIhecDGc1MgtZAKKiaGSgqrbaXyQihmzWwN0bILrroQZ3vvbrmz1Lq6I1YKo92GEQT0yhyaoargTSHk+gqGIulKg2pYV9WSBfXQRnrpzZaN/cyg1xusZZH/cGTHRMpxN1RQd0XJBI1ahvc6p+SJOE/jJKlwls63r3wxqwMBlIciTy8/OHyigiTioZM+/8k+En6QB+R7bsiKbQRTr+aVuuNh9QnqW2JKrFZwb6rKy5Naibc0c1NinFEKOj3KWV+JCL+qNudMjiyE+Vv/gUvsXQr/D3L2+beadSfTGWRCGY+WDvrUHQ8sHxqzsLb0+X3j/C/cnoTOknEvsgAAAABJRU5ErkJggg==',
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
}
