<?php
namespace BossBaby;

class Telegram
{
    public static function func_telegram_print_arr($arr = null) 
    {
        if (!$arr) return 'No data found!';
        $text = "\n";
        foreach ($arr as $key => $value) {
            if (is_object($value)) {
                $text .= ucfirst($key) . ': ' . serialize($value) . "\n";
            }
            else {
                $text .= ucfirst($key) . ': ' . $value . "\n";
            }
        }
        $text .= "\n";

        return $text;
    }

    public static function format_xbt_price_for_telegram()
    {
        $price = '';
        $arr = \BossBaby\Bitmex::func_get_current_price();

        $_current_price = 0;
        $last_orig = $arr['last'];
        $last_sess = (isset($_current_price)) ? $_current_price : 0;
        $_current_price = $last_orig;
        // $arr['sess_last'] = $last_sess;
        
        if (!isset($_current_price)) {
            if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '👆 ' . $arr['last'];
            elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '👇 ' . $arr['last'];
        }
        else {
            if ($arr['last'] >= $last_sess) $arr['last'] = '👆 ' . $arr['last'];
            elseif ($arr['last'] < $last_sess) $arr['last'] = '👇 ' . $arr['last'];
        }
        if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '👆 ' . ($arr['lastChangePcnt'] * 100) . '%';
        elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '👇 ' . ($arr['lastChangePcnt'] * 100) . '%';
        else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

        $arr['Changed'] = $arr['lastChangePcnt']; unset($arr['lastChangePcnt']);

        $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
        $price = str_replace('Symbol: XBTUSD', 'Giá *XBT/USD* trên Bitmex', $price);

        return $price;
    }
    
    public static function format_alt_price_for_telegram($coin_name = '')
    {
        $price = '';
        $coin_name = strtoupper($coin_name);
        
        $arr = \BossBaby\Binance::get_coin_price($coin_name);
        if (is_array($arr) and count($arr)) {
            // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin_name));
            // $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $price .= 'Giá *' . $coin_name . '* trên Binance:' . PHP_EOL;
            foreach ($arr as $key => $value) {
                $price .= str_replace($coin_name, $coin_name . '/', $key) . ': ' . $value . PHP_EOL;
            }
        }

        $arr = \BossBaby\Bittrex::get_coin_price($coin_name);
        if (is_array($arr) and count($arr)) {
            $price .= PHP_EOL;
            // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin_name));
            // $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $price .= 'Giá *' . $coin_name . '* trên Bittrex:' . PHP_EOL;
            foreach ($arr as $key => $value) {
                $price .= str_replace('-', '/', $key) . ': ' . $value . PHP_EOL;
            }
        }

        return $price;
    }

    public static function get_coin_pulse_binance()
    {
        $return = [];

        // Get list current coin
        $list_coin = \BossBaby\Binance::get_list_coin();
        // \BossBaby\Utility::writeLog('list_coin:'.serialize($list_coin));

        // List coin should to ignore
        $list_ignore = ['HOT'];

        $arr = [];
        $arr['last_updated'] = date('Y-m-d H:i:s');
        $arr['last_updated_unix'] = time();

        // File store coin data
        $file = LOGS_DIR . '/binance_coins.php';
        // \BossBaby\Utility::writeLog('file:'.serialize($file));

        $min = -5; //%
        $max = 5; //%

        // Processing on list coin
        if (is_array($list_coin) and count($list_coin)) {
            if (is_file($file) and file_exists($file)) {
                // Read data from file
                $old_data = \BossBaby\Config::read_file($file);
                $old_data = \BossBaby\Utility::object_to_array(json_decode($old_data));
                // dump('$old_data'); dump($old_data);

                $current_time = time();
                // dump('$current_time'); dump($current_time);
                
                if (!json_last_error() and $old_data) {
                    foreach ($list_coin as $coin => $new_price) {
                        if (strrpos($coin, 'BTC') === false) continue;
                        $calc_10s = $calc_1m = $calc_5m = 0;
                        $coin_name = (strrpos($coin, 'BTC') !== false) ? str_replace('BTC', '', $coin) : $coin;
                        // echo '<hr/>';
                        // dump('$coin'); dump($coin);
                        // dump('$coin_name'); dump($coin_name);
                        // dump('$new_price'); dump($new_price);
                        if (in_array($coin_name, $list_ignore)) continue;

                        // Check for 10s
                        $changed_10s = '';
                        $old_time_10s = (isset($old_data['updated_10s']) and $old_data['updated_10s']) ? $old_data['updated_10s'] : 0;
                        $changed_time_10s = $current_time - $old_time_10s;
                        // dump('changed_time_10s'); dump($changed_time_10s);
                        if ($changed_time_10s >= 10) {
                            // dump(isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s']));
                            if (isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s'])) {
                                $tmp_arr = $old_data['10s'];
                                // dump('array_key_exists($coin, $tmp_arr10s)'); dump(array_key_exists($coin, $tmp_arr));
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    // dump('changed_10s$old_price'); dump($old_price);
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_10s = round($cal, 2);
                                    if ($calc_10s > $max or $calc_10s < $min)
                                        $changed_10s = ' (<b>' . $calc_10s . '</b>%/10s)';
                                    else
                                        $changed_10s = ' (' . $calc_10s . '%/10s)';

                                    if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                                    $arr['changed_10s'][$coin] = $changed_10s;

                                    if (!isset($arr['10s_ago'])) $arr['10s_ago'] = [];
                                    $arr['10s_ago'][$coin] = $old_price;
                                }
                            }

                            $arr['10s'] = $list_coin;
                            $arr['updated_10s'] = $current_time;
                        }
                        else {
                            $arr['10s'] = $old_data['10s'];
                            $arr['updated_10s'] = $old_time_10s;

                            if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                            if (isset($old_data['changed_10s'][$coin])) {
                                $changed_10s = $arr['changed_10s'][$coin] = $old_data['changed_10s'][$coin];
                            }

                            if (!isset($arr['10s_ago'])) $arr['10s_ago'] = [];
                            if (isset($old_data['10s_ago'][$coin])) {
                                $arr['10s_ago'][$coin] = $old_data['10s_ago'][$coin];
                            }
                        }

                        // Check for 1m
                        $changed_1m = '';
                        $old_time_1m = (isset($old_data['updated_1m']) and $old_data['updated_1m']) ? $old_data['updated_1m'] : 0;
                        $changed_time_1m = $current_time - $old_time_1m;
                        // dump('changed_time_1m'); dump($changed_time_1m);
                        if ($changed_time_1m >= 1*60) {
                            // dump(isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m']));
                            if (isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m'])) {
                                $tmp_arr = $old_data['1m'];
                                // dump('array_key_exists($coin, $tmp_arr1m)'); dump(array_key_exists($coin, $tmp_arr));
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    // dump('changed_1m$old_price'); dump($old_price);
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_1m = round($cal, 2);
                                    if ($calc_1m > $max or $calc_1m < $min)
                                        $changed_1m = ' (<b>' . $calc_1m . '</b>%/1m)';
                                    else
                                        $changed_1m = ' (' . $calc_1m . '%/1m)';

                                    if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                                    $arr['changed_1m'][$coin] = $changed_1m;

                                    if (!isset($arr['1m_ago'])) $arr['1m_ago'] = [];
                                    $arr['1m_ago'][$coin] = $old_price;
                                }
                            }

                            $arr['1m'] = $list_coin;
                            $arr['updated_1m'] = $current_time;
                        }
                        else {
                            $arr['1m'] = $old_data['1m'];
                            $arr['updated_1m'] = $old_time_1m;

                            if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                            if (isset($old_data['changed_1m'][$coin])) {
                                $changed_1m = $arr['changed_1m'][$coin] = $old_data['changed_1m'][$coin];
                            }

                            if (!isset($arr['1m_ago'])) $arr['1m_ago'] = [];
                            if (isset($old_data['1m_ago'][$coin])) {
                                $arr['1m_ago'][$coin] = $old_data['1m_ago'][$coin];
                            }
                        }

                        // Check for 5m
                        $changed_5m = '';
                        $old_time_5m = (isset($old_data['updated_5m']) and $old_data['updated_5m']) ? $old_data['updated_5m'] : 0;
                        $changed_time_5m = $current_time - $old_time_5m;
                        // dump('changed_time_5m'); dump($changed_time_5m);
                        if ($changed_time_5m >= 5*60) {
                            // dump(isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m']));
                            if (isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m'])) {
                                $tmp_arr = $old_data['5m'];
                                // dump('array_key_exists($coin, $tmp_arr5m)'); dump(array_key_exists($coin, $tmp_arr));
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    // dump('changed_5m$old_price'); dump($old_price);
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_5m = round($cal, 2);
                                    if ($calc_5m > $max or $calc_5m < $min)
                                        $changed_5m = ' (<b>' . $calc_5m . '</b>%/5m)';
                                    else
                                        $changed_5m = ' (' . $calc_5m . '%/5m)';
                                    
                                    if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                                    $arr['changed_5m'][$coin] = $changed_5m;

                                    if (!isset($arr['5m_ago'])) $arr['5m_ago'] = [];
                                    $arr['5m_ago'][$coin] = $old_price;
                                }
                            }

                            $arr['5m'] = $list_coin;
                            $arr['updated_5m'] = $current_time;
                        }
                        else {
                            $arr['5m'] = $old_data['5m'];
                            $arr['updated_5m'] = $old_time_5m;

                            if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                            if (isset($old_data['changed_5m'][$coin])) {
                                $changed_5m = $arr['changed_5m'][$coin] = $old_data['changed_5m'][$coin];
                            }

                            if (!isset($arr['5m_ago'])) $arr['5m_ago'] = [];
                            if (isset($old_data['5m_ago'][$coin])) {
                                $arr['5m_ago'][$coin] = $old_data['5m_ago'][$coin];
                            }
                        }

                        // dump('$calc_10s'); dump($calc_10s);
                        // dump('$calc_1m'); dump($calc_1m);
                        // dump('$calc_5m'); dump($calc_5m);
                        // dump('$changed_10s'); dump($changed_10s);
                        // dump('$changed_1m'); dump($changed_1m);
                        // dump('$changed_5m'); dump($changed_5m);
                        
                        // Check to add to returns
                        if (($calc_10s > $max or $calc_10s < $min) or ($calc_1m > $max or $calc_1m < $min) or ($calc_5m > $max or $calc_5m < $min)) {
                            // https://www.binance.com/trade.html?symbol=BTC_USDT
                            $text_out_link = \BossBaby\Utility::func_clean_double_space($changed_5m . $changed_1m . $changed_10s);
                            $tmp_str = PHP_EOL . '<a href="https://www.binance.com/trade.html?symbol=' . $coin_name . '_BTC">' . $coin_name . '</a> ' . $text_out_link;
                            if (isset($arr['10s_ago']) and isset($arr['10s_ago'][$coin])) $tmp_str .= PHP_EOL . '<b>10 giây</b> trước: ' . $arr['10s_ago'][$coin];
                            if (isset($arr['1m_ago']) and isset($arr['1m_ago'][$coin])) $tmp_str .= PHP_EOL . '<b>1 phút</b> trước: ' . $arr['1m_ago'][$coin];
                            if (isset($arr['5m_ago']) and isset($arr['5m_ago'][$coin])) $tmp_str .= PHP_EOL . '<b>5 phút</b> trước: ' . $arr['5m_ago'][$coin];
                            $tmp_str .= PHP_EOL . 'giá hiện tại: <b>' . $new_price . '</b>';
                            $return[] = $tmp_str . PHP_EOL;
                            // break;
                        }
                    }
                }

                if (!isset($old_data['10s']) or !is_array($old_data['10s'])) $arr['10s'] = $list_coin;
                if (!isset($old_data['1m']) or !is_array($old_data['1m'])) $arr['1m'] = $list_coin;
                if (!isset($old_data['5m']) or !is_array($old_data['5m'])) $arr['5m'] = $list_coin;
                if (!isset($old_data['updated_10s']) or !$old_data['updated_10s']) $arr['updated_10s'] = $current_time;
                if (!isset($old_data['updated_1m']) or !$old_data['updated_1m']) $arr['updated_1m'] = $current_time;
                if (!isset($old_data['updated_5m']) or !$old_data['updated_5m']) $arr['updated_5m'] = $current_time;
            }
            else {
                // Put current coin data into array
                $arr['10s'] = $arr['1m'] = $arr['5m'] = $list_coin;
                $arr['updated_10s'] = $arr['updated_1m'] = $arr['updated_5m'] = time();
                $arr['changed_10s'] = $arr['changed_1m'] = $arr['changed_5m'] = [];
            }
            
            // dump('$arr'); dump($arr);
            // Write again into file
            $arr = json_encode($arr);
            \BossBaby\Config::write_file($file, $arr);
        }

        // dump('$return'); dump($return);die;
        $text = '';
        if ($return) {
            $text = 'Chú ý giá coin thay đổi trên <a href="https://www.binance.com/?ref=13132993">Binance</a>:' . PHP_EOL;
            foreach ($return as $value) {
                $text .= $value;
            }
        }

        return $text;
    }

    public static function get_coin_pulse_bittrex()
    {
        $return = [];

        // Get list current coin
        $list_coin = \BossBaby\Bittrex::get_list_coin();
        // \BossBaby\Utility::writeLog('list_coin:'.serialize($list_coin));
        $list_coin = \BossBaby\Utility::object_to_array($list_coin);

        if (!$list_coin or $list_coin['success'] != true or !$list_coin['result'])
            return $return;
        $list_coin = $list_coin['result'];

        // List coin should to ignore
        $list_ignore = [];

        $arr = [];
        $arr['last_updated'] = date('Y-m-d H:i:s');
        $arr['last_updated_unix'] = time();

        // File store coin data
        $file = LOGS_DIR . '/bittrex_coins.php';
        // \BossBaby\Utility::writeLog('file:'.serialize($file));

        $min = -5; //%
        $max = 5; //%

        // Processing on list coin
        if (is_array($list_coin) and count($list_coin)) {
            $tmp_list = $list_coin;
            $list_coin = [];
            foreach ($tmp_list as $pos => $coin) {
                $list_coin[$coin['MarketName']] = number_format($coin['Last'], 8);
            }
            unset($tmp_list);

            if (is_file($file) and file_exists($file)) {
                // Read data from file
                $old_data = \BossBaby\Config::read_file($file);
                $old_data = \BossBaby\Utility::object_to_array(json_decode($old_data));
                // dump('$old_data'); dump($old_data);

                $current_time = time();
                // dump('$current_time'); dump($current_time);
                
                if (!json_last_error() and $old_data) {
                    foreach ($list_coin as $coin => $new_price) {
                        if (strrpos($coin, 'BTC-') === false) continue;
                        $calc_10s = $calc_1m = $calc_5m = 0;
                        $coin_name = (strrpos($coin, 'BTC-') !== false) ? str_replace('BTC-', '', $coin) : $coin;
                        // echo '<hr/>';
                        // dump('$coin'); dump($coin);
                        // dump('$coin_name'); dump($coin_name);
                        // dump('$new_price'); dump($new_price);
                        if (in_array($coin_name, $list_ignore)) continue;

                        // Check for 10s
                        $changed_10s = '';
                        $old_time_10s = (isset($old_data['updated_10s']) and $old_data['updated_10s']) ? $old_data['updated_10s'] : 0;
                        $changed_time_10s = $current_time - $old_time_10s;
                        // dump('changed_time_10s'); dump($changed_time_10s);
                        if ($changed_time_10s >= 10) {
                            // dump(isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s']));
                            if (isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s'])) {
                                $tmp_arr = $old_data['10s'];
                                // dump('array_key_exists($coin, $tmp_arr10s)'); dump(array_key_exists($coin, $tmp_arr));
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    // dump('changed_10s$old_price'); dump($old_price);
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_10s = round($cal, 2);
                                    if ($calc_10s > $max or $calc_10s < $min)
                                        $changed_10s = ' (<b>' . $calc_10s . '</b>%/10s)';
                                    else
                                        $changed_10s = ' (' . $calc_10s . '%/10s)';

                                    if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                                    $arr['changed_10s'][$coin] = $changed_10s;

                                    if (!isset($arr['10s_ago'])) $arr['10s_ago'] = [];
                                    $arr['10s_ago'][$coin] = $old_price;
                                }
                            }

                            $arr['10s'] = $list_coin;
                            $arr['updated_10s'] = $current_time;
                        }
                        else {
                            $arr['10s'] = $old_data['10s'];
                            $arr['updated_10s'] = $old_time_10s;

                            if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                            if (isset($old_data['changed_10s'][$coin])) {
                                $changed_10s = $arr['changed_10s'][$coin] = $old_data['changed_10s'][$coin];
                            }

                            if (!isset($arr['10s_ago'])) $arr['10s_ago'] = [];
                            if (isset($old_data['10s_ago'][$coin])) {
                                $arr['10s_ago'][$coin] = $old_data['10s_ago'][$coin];
                            }
                        }

                        // Check for 1m
                        $changed_1m = '';
                        $old_time_1m = (isset($old_data['updated_1m']) and $old_data['updated_1m']) ? $old_data['updated_1m'] : 0;
                        $changed_time_1m = $current_time - $old_time_1m;
                        // dump('changed_time_1m'); dump($changed_time_1m);
                        if ($changed_time_1m >= 1*60) {
                            // dump(isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m']));
                            if (isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m'])) {
                                $tmp_arr = $old_data['1m'];
                                // dump('array_key_exists($coin, $tmp_arr1m)'); dump(array_key_exists($coin, $tmp_arr));
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    // dump('changed_1m$old_price'); dump($old_price);
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_1m = round($cal, 2);
                                    if ($calc_1m > $max or $calc_1m < $min)
                                        $changed_1m = ' (<b>' . $calc_1m . '</b>%/1m)';
                                    else
                                        $changed_1m = ' (' . $calc_1m . '%/1m)';

                                    if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                                    $arr['changed_1m'][$coin] = $changed_1m;

                                    if (!isset($arr['1m_ago'])) $arr['1m_ago'] = [];
                                    $arr['1m_ago'][$coin] = $old_price;
                                }
                            }

                            $arr['1m'] = $list_coin;
                            $arr['updated_1m'] = $current_time;
                        }
                        else {
                            $arr['1m'] = $old_data['1m'];
                            $arr['updated_1m'] = $old_time_1m;

                            if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                            if (isset($old_data['changed_1m'][$coin])) {
                                $changed_1m = $arr['changed_1m'][$coin] = $old_data['changed_1m'][$coin];
                            }

                            if (!isset($arr['1m_ago'])) $arr['1m_ago'] = [];
                            if (isset($old_data['1m_ago'][$coin])) {
                                $arr['1m_ago'][$coin] = $old_data['1m_ago'][$coin];
                            }
                        }

                        // Check for 5m
                        $changed_5m = '';
                        $old_time_5m = (isset($old_data['updated_5m']) and $old_data['updated_5m']) ? $old_data['updated_5m'] : 0;
                        $changed_time_5m = $current_time - $old_time_5m;
                        // dump('changed_time_5m'); dump($changed_time_5m);
                        if ($changed_time_5m >= 5*60) {
                            // dump(isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m']));
                            if (isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m'])) {
                                $tmp_arr = $old_data['5m'];
                                // dump('array_key_exists($coin, $tmp_arr5m)'); dump(array_key_exists($coin, $tmp_arr));
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    // dump('changed_5m$old_price'); dump($old_price);
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_5m = round($cal, 2);
                                    if ($calc_5m > $max or $calc_5m < $min)
                                        $changed_5m = ' (<b>' . $calc_5m . '</b>%/5m)';
                                    else
                                        $changed_5m = ' (' . $calc_5m . '%/5m)';
                                    
                                    if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                                    $arr['changed_5m'][$coin] = $changed_5m;

                                    if (!isset($arr['5m_ago'])) $arr['5m_ago'] = [];
                                    $arr['5m_ago'][$coin] = $old_price;
                                }
                            }

                            $arr['5m'] = $list_coin;
                            $arr['updated_5m'] = $current_time;
                        }
                        else {
                            $arr['5m'] = $old_data['5m'];
                            $arr['updated_5m'] = $old_time_5m;

                            if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                            if (isset($old_data['changed_5m'][$coin])) {
                                $changed_5m = $arr['changed_5m'][$coin] = $old_data['changed_5m'][$coin];
                            }

                            if (!isset($arr['5m_ago'])) $arr['5m_ago'] = [];
                            if (isset($old_data['5m_ago'][$coin])) {
                                $arr['5m_ago'][$coin] = $old_data['5m_ago'][$coin];
                            }
                        }

                        // dump('$calc_10s'); dump($calc_10s);
                        // dump('$calc_1m'); dump($calc_1m);
                        // dump('$calc_5m'); dump($calc_5m);
                        // dump('$changed_10s'); dump($changed_10s);
                        // dump('$changed_1m'); dump($changed_1m);
                        // dump('$changed_5m'); dump($changed_5m);
                        
                        // Check to add to returns
                        if (($calc_10s > $max or $calc_10s < $min) or ($calc_1m > $max or $calc_1m < $min) or ($calc_5m > $max or $calc_5m < $min)) {
                            // https://www.binance.com/trade.html?symbol=BTC_USDT
                            $text_out_link = \BossBaby\Utility::func_clean_double_space($changed_5m . $changed_1m . $changed_10s);
                            $tmp_str = PHP_EOL . '<a href="https://bittrex.com/Market/Index?MarketName=' . $coin . '">' . $coin_name . '</a> ' . $text_out_link;
                            if (isset($arr['10s_ago']) and isset($arr['10s_ago'][$coin])) $tmp_str .= PHP_EOL . '<b>10 giây</b> trước: ' . $arr['10s_ago'][$coin];
                            if (isset($arr['1m_ago']) and isset($arr['1m_ago'][$coin])) $tmp_str .= PHP_EOL . '<b>1 phút</b> trước: ' . $arr['1m_ago'][$coin];
                            if (isset($arr['5m_ago']) and isset($arr['5m_ago'][$coin])) $tmp_str .= PHP_EOL . '<b>5 phút</b> trước: ' . $arr['5m_ago'][$coin];
                            $tmp_str .= PHP_EOL . 'giá hiện tại: <b>' . $new_price . '</b>';
                            $return[] = $tmp_str . PHP_EOL;
                            // break;
                        }
                    }
                }

                if (!isset($old_data['10s']) or !is_array($old_data['10s'])) $arr['10s'] = $list_coin;
                if (!isset($old_data['1m']) or !is_array($old_data['1m'])) $arr['1m'] = $list_coin;
                if (!isset($old_data['5m']) or !is_array($old_data['5m'])) $arr['5m'] = $list_coin;
                if (!isset($old_data['updated_10s']) or !$old_data['updated_10s']) $arr['updated_10s'] = $current_time;
                if (!isset($old_data['updated_1m']) or !$old_data['updated_1m']) $arr['updated_1m'] = $current_time;
                if (!isset($old_data['updated_5m']) or !$old_data['updated_5m']) $arr['updated_5m'] = $current_time;
            }
            else {
                // Put current coin data into array
                $arr['10s'] = $arr['1m'] = $arr['5m'] = $list_coin;
                $arr['updated_10s'] = $arr['updated_1m'] = $arr['updated_5m'] = time();
                $arr['changed_10s'] = $arr['changed_1m'] = $arr['changed_5m'] = [];
            }
            
            // dump('$arr'); dump($arr);
            // Write again into file
            $arr = json_encode($arr);
            \BossBaby\Config::write_file($file, $arr);
        }

        // dump('$return'); dump($return);die;
        $text = '';
        if ($return) {
            $text = 'Chú ý giá coin thay đổi trên <a href="https://bittrex.com/Market">Bittrex</a>:' . PHP_EOL;
            foreach ($return as $value) {
                $text .= $value;
            }
        }

        return $text;
    }
}