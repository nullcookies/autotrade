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
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

        $file = CONFIG_DIR . '/bitmex_coins.php';
        $list_coin = \BossBaby\Config::read_file($file);
        $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
        if (!json_last_error() and $list_coin and isset($list_coin['symbols']['XBTUSD'])) {
            $arr = $list_coin['symbols']['XBTUSD'];
        }
        else {
            global $environment;
            $environment->bitmex_instance = new \Bitmex($environment->bitmex->accounts->{1}->apiKey, $environment->bitmex->accounts->{1}->apiSecret);
            $arr = \BossBaby\Bitmex::func_get_current_price($environment->bitmex_instance);
        }

        if (!$arr or !$arr['symbol'])
            return null;

        $arr = array(
            "symbol" => $arr['symbol'],
            "last" => $arr['lastPrice'],
            "bid" => $arr['bidPrice'],
            "ask" => $arr['askPrice'],
            "high" => $arr['highPrice'],
            "low" => $arr['lowPrice'],
            "lastChangePcnt" => $arr["lastChangePcnt"],
            "markPrice" => $arr["markPrice"],
        );
        
        $price = '';

        $_current_price = 0;
        $last_orig = $arr['last'];
        $last_sess = (isset($_current_price)) ? $_current_price : 0;
        $_current_price = $last_orig;
        // $arr['sess_last'] = $last_sess;
        
        if (!isset($_current_price)) {
            if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '🔵 ' . $arr['last'];
            elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '🔴 ' . $arr['last'];
        }
        else {
            if ($arr['last'] >= $last_sess) $arr['last'] = '🔵 ' . $arr['last'];
            elseif ($arr['last'] < $last_sess) $arr['last'] = '🔴 ' . $arr['last'];
        }
        if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '🔵 ' . ($arr['lastChangePcnt'] * 100) . '%';
        elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '🔴 ' . ($arr['lastChangePcnt'] * 100) . '%';
        else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

        $arr['Changed'] = $arr['lastChangePcnt']; unset($arr['lastChangePcnt']);

        $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
        $price = str_replace('Symbol: XBTUSD', '*XBT/USD* on Bitmex', $price);

        return $price;
    }
    
    public static function format_alt_price_for_telegram($coin_name = '')
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::coin:'.serialize($coin_name));

        $price = '';
        $coin_name = strtoupper($coin_name);
        
        $arr = \BossBaby\Binance::get_coin_price($coin_name);
        // \BossBaby\Utility::writeLog('-coin:'.serialize($coin_name).PHP_EOL.'arr_binance:'.serialize($arr));
        if (is_array($arr) and count($arr)) {
            // $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $price .= '*' . $coin_name . '* on Binance:' . PHP_EOL;
            foreach ($arr as $key => $value) {
                $price .= str_replace($coin_name, $coin_name . '/', $key) . ': ' . $value . PHP_EOL;
            }
        }

        $arr = \BossBaby\Bittrex::get_coin_price($coin_name);
        // \BossBaby\Utility::writeLog('-coin:'.serialize($coin_name).PHP_EOL.'arr_bittrex:'.serialize($arr));
        if (is_array($arr) and count($arr)) {
            $price .= PHP_EOL;
            // $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $price .= '*' . $coin_name . '* on Bittrex:' . PHP_EOL;
            foreach ($arr as $key => $value) {
                $price .= str_replace($coin_name, $coin_name . '/', $key) . ': ' . $value . PHP_EOL;
            }
        }

        $arr = \BossBaby\HoubiPro::get_coin_price($coin_name);
        // \BossBaby\Utility::writeLog('-coin:'.serialize($coin_name).PHP_EOL.'arr_houbi:'.serialize($arr));
        if (is_array($arr) and count($arr)) {
            $price .= PHP_EOL;
            // $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $price .= '*' . $coin_name . '* on HoubiPro:' . PHP_EOL;
            foreach ($arr as $key => $value) {
                $price .= str_replace($coin_name, $coin_name . '/', $key) . ': ' . $value . PHP_EOL;
            }
        }

        return $price;
    }

    public static function get_coin_pulse_binance($min = -5, $max = 5)
    {
        $return = ['telegram' => [], 'discord' => []];

        // Get list current coin
        $file = CONFIG_DIR . '/binance_coins.php';
        $list_coin = \BossBaby\Config::read_file($file);
        $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
        if (!json_last_error() and $list_coin)
            $list_coin = $list_coin['symbols'];
        if ($list_coin) {
            $list_coin_tmp = [];
            foreach ($list_coin as $coin => $item) {
                $list_coin_tmp[$coin] = $item['price'];
            }
            $list_coin = $list_coin_tmp;
            unset($list_coin_tmp);
        }
        if (!$list_coin)
            $list_coin = \BossBaby\Binance::get_list_coin();
        
        // List coin should to ignore
        $list_ignore = ['HOT', 'USDT', 'HSR'];

        $arr = [];
        $arr['last_updated'] = date('Y-m-d H:i:s');
        $arr['last_updated_unix'] = time();

        // File store coin data
        $file = CONFIG_DIR . '/binance_coins_pulse.php';

        // $min = -5; //%
        // $max = 5; //%

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
                        // echo '<hr/>';
                        // dump('$coin'); dump($coin);
                        if (strrpos($coin, 'BTC') === false) continue;
                        $new_price = number_format($new_price, 8);
                        // dump('$new_price'); dump($new_price);
                        if ($new_price <= 0) continue;
                        $calc_10s = $calc_1m = $calc_5m = $calc_1h = 0;
                        $coin_name = (strrpos($coin, 'BTC') !== false) ? str_replace('BTC', '', $coin) : $coin;
                        // dump('$coin_name'); dump($coin_name);
                        if (in_array($coin_name, $list_ignore)) continue;

                        // Check for 1h
                        $changed_1h = '';
                        $old_time_1h = (isset($old_data['updated_1h']) and $old_data['updated_1h']) ? $old_data['updated_1h'] : 0;
                        $changed_time_1h = $current_time - $old_time_1h;
                        // dump('changed_time_1h'); dump($changed_time_1h);
                        if ($changed_time_1h >= 60*60) {
                            $arr['1h'] = $list_coin;
                            $arr['updated_1h'] = $current_time;
                        }
                        else {
                            $arr['1h'] = $old_data['1h'];
                            $arr['updated_1h'] = $old_time_1h;
                        }
                        if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                        if (isset($old_data['changed_1h'][$coin])) {
                            $changed_1h = $arr['changed_1h'][$coin] = $old_data['changed_1h'][$coin];
                        }
                        // dump(isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h']));
                        if (isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h'])) {
                            $tmp_arr = $old_data['1h'];
                            // dump('array_key_exists($coin, $tmp_arr1h)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_1h$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_1h = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                                    $changed_1h = $arr['changed_1h'][$coin] = $calc_1h;
                                }
                            }
                        }

                        // Check for 5m
                        $changed_5m = '';
                        $old_time_5m = (isset($old_data['updated_5m']) and $old_data['updated_5m']) ? $old_data['updated_5m'] : 0;
                        $changed_time_5m = $current_time - $old_time_5m;
                        // dump('changed_time_5m'); dump($changed_time_5m);
                        if ($changed_time_5m >= 5*60) {
                            $arr['5m'] = $list_coin;
                            $arr['updated_5m'] = $current_time;
                        }
                        else {
                            $arr['5m'] = $old_data['5m'];
                            $arr['updated_5m'] = $old_time_5m;
                        }
                        if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                        if (isset($old_data['changed_5m'][$coin])) {
                            $changed_5m = $arr['changed_5m'][$coin] = $old_data['changed_5m'][$coin];
                        }
                        // dump(isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m']));
                        if (isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m'])) {
                            $tmp_arr = $old_data['5m'];
                            // dump('array_key_exists($coin, $tmp_arr5m)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_5m$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_5m = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                                    $changed_5m = $arr['changed_5m'][$coin] = $calc_5m;
                                }
                            }
                        }

                        // Check for 1m
                        $changed_1m = '';
                        $old_time_1m = (isset($old_data['updated_1m']) and $old_data['updated_1m']) ? $old_data['updated_1m'] : 0;
                        $changed_time_1m = $current_time - $old_time_1m;
                        // dump('changed_time_1m'); dump($changed_time_1m);
                        if ($changed_time_1m >= 1*60) {
                            $arr['1m'] = $list_coin;
                            $arr['updated_1m'] = $current_time;
                        }
                        else {
                            $arr['1m'] = $old_data['1m'];
                            $arr['updated_1m'] = $old_time_1m;
                        }
                        if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                        if (isset($old_data['changed_1m'][$coin])) {
                            $changed_1m = $arr['changed_1m'][$coin] = $old_data['changed_1m'][$coin];
                        }
                        // dump(isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m']));
                        if (isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m'])) {
                            $tmp_arr = $old_data['1m'];
                            // dump('array_key_exists($coin, $tmp_arr1m)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_1m$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_1m = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                                    $changed_1m = $arr['changed_1m'][$coin] = $calc_1m;
                                }
                            }
                        }

                        // Check for 10s
                        $changed_10s = '';
                        $old_time_10s = (isset($old_data['updated_10s']) and $old_data['updated_10s']) ? $old_data['updated_10s'] : 0;
                        $changed_time_10s = $current_time - $old_time_10s;
                        // dump('changed_time_10s'); dump($changed_time_10s);
                        if ($changed_time_10s >= 10) {
                            $arr['10s'] = $list_coin;
                            $arr['updated_10s'] = $current_time;
                        }
                        else {
                            $arr['10s'] = $old_data['10s'];
                            $arr['updated_10s'] = $old_time_10s;
                        }
                        if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                        if (isset($old_data['changed_10s'][$coin])) {
                            $changed_10s = $arr['changed_10s'][$coin] = $old_data['changed_10s'][$coin];
                        }
                        // dump(isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s']));
                        if (isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s'])) {
                            $tmp_arr = $old_data['10s'];
                            // dump('array_key_exists($coin, $tmp_arr10s)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_10s$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_10s = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                                    $changed_10s = $arr['changed_10s'][$coin] = $calc_10s;
                                }
                            }
                        }
                        
                        // Check to add to returns
                        if ((((($calc_10s >= 0) and ($calc_10s > $max) and (isset($old_data['changed_10s'][$coin]) and $calc_10s > $old_data['changed_10s'][$coin])) or (($calc_10s < 0) and ($calc_10s < $min) and (isset($old_data['changed_10s'][$coin]) and $calc_10s < $old_data['changed_10s'][$coin]))) and ($changed_time_10s >= 10)) 
                            or (((($calc_1m >= 0) and ($calc_1m > $max) and (isset($old_data['changed_1m'][$coin]) and $calc_1m > $old_data['changed_1m'][$coin])) or (($calc_1m < 0) and ($calc_1m < $min) and (isset($old_data['changed_1m'][$coin]) and $calc_1m < $old_data['changed_1m'][$coin]))) and ($changed_time_1m >= 1*60)) 
                            or (((($calc_5m >= 0) and ($calc_5m > $max) and (isset($old_data['changed_5m'][$coin]) and $calc_5m > $old_data['changed_5m'][$coin])) or (($calc_5m < 0) and ($calc_5m < $min) and (isset($old_data['changed_5m'][$coin]) and $calc_5m < $old_data['changed_5m'][$coin]))) and ($changed_time_5m >= 5*60)) 
                            or (((($calc_1h >= 0) and ($calc_1h > $max) and (isset($old_data['changed_1h'][$coin]) and $calc_1h > $old_data['changed_1h'][$coin])) or (($calc_1h < 0) and ($calc_1h < $min) and (isset($old_data['changed_1h'][$coin]) and $calc_1h < $old_data['changed_1h'][$coin]))) and ($changed_time_1h >= 60*60))
                        ) {
                            if ($calc_1h > $max or $calc_1h < $min) {
                                $changed_1h = ' (<b>' . (float) $calc_1h . '</b>%/1h)';
                            }
                            elseif ($calc_5m > $max or $calc_5m < $min) {
                                $changed_5m = ' (<b>' . (float) $calc_5m . '</b>%/5m)';
                            }
                            elseif ($calc_1m > $max or $calc_1m < $min) {
                                $changed_1m = ' (<b>' . (float) $calc_1m . '</b>%/1m)';
                            }
                            elseif ($calc_10s > $max or $calc_10s < $min) {
                                $changed_10s = ' (<b>' . (float) $calc_10s . '</b>%/10s)';
                            }
                            
                            if ($changed_1h == '' and isset($old_data['changed_1h'][$coin])) $changed_1h = ' (' . (float) $old_data['changed_1h'][$coin] . '%/1h)';
                            if ($changed_5m == '' and isset($old_data['changed_5m'][$coin])) $changed_5m = ' (' . (float) $old_data['changed_5m'][$coin] . '%/5m)';
                            if ($changed_1m == '' and isset($old_data['changed_1m'][$coin])) $changed_1m = ' (' . (float) $old_data['changed_1m'][$coin] . '%/1m)';
                            if ($changed_10s == '' and isset($old_data['changed_10s'][$coin])) $changed_10s = ' (' . (float) $old_data['changed_10s'][$coin] . '%/10s)';

                            if (strpos($changed_1h, '(') === false) $changed_1h = ' (' . (float) $changed_1h . '%/1h)';
                            if (strpos($changed_5m, '(') === false) $changed_5m = ' (' . (float) $changed_5m . '%/5m)';
                            if (strpos($changed_1m, '(') === false) $changed_1m = ' (' . (float) $changed_1m . '%/1m)';
                            if (strpos($changed_10s, '(') === false) $changed_10s = ' (' . (float) $changed_10s . '%/10s)';

                            $text_out_link = \BossBaby\Utility::func_clean_double_space($changed_1h . $changed_5m . $changed_1m . $changed_10s);

                            // Format for Telegram
                            // https://www.binance.com/trade.html?symbol=BTC_USDT
                            $tmp_str = '<a href="https://www.binance.com/trade.html?symbol=' . $coin_name . '_BTC">' . $coin_name . '</a> ' . $text_out_link;
                            if (isset($old_data['10s']) and isset($old_data['10s'][$coin])) $tmp_str .= PHP_EOL . '10s ago: ' . number_format($old_data['10s'][$coin], 8);
                            if (isset($old_data['1m']) and isset($old_data['1m'][$coin])) $tmp_str .= PHP_EOL . '1m ago: ' . number_format($old_data['1m'][$coin], 8);
                            if (isset($old_data['5m']) and isset($old_data['5m'][$coin])) $tmp_str .= PHP_EOL . '5m ago: ' . number_format($old_data['5m'][$coin], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin])) $tmp_str .= PHP_EOL . '1h ago: ' . number_format($old_data['1h'][$coin], 8);
                            $tmp_str .= PHP_EOL . 'last price: <b>' . $new_price . '</b>';
                            $return['telegram'][] = $tmp_str . PHP_EOL;

                            // Format for Discord
                            $tmp_str = "\n\r" . PHP_EOL . '<https://www.binance.com/trade.html?symbol=' . $coin_name . '_BTC>';
                            $text_out_link = str_replace('<b>', '**', $text_out_link);
                            $text_out_link = str_replace('</b>', '**', $text_out_link);
                            $tmp_str .= PHP_EOL . '**#' . $coin_name . '** ' . $text_out_link;
                            if (isset($old_data['10s']) and isset($old_data['10s'][$coin])) $tmp_str .= PHP_EOL . '10s ago: ' . number_format($old_data['10s'][$coin], 8);
                            if (isset($old_data['1m']) and isset($old_data['1m'][$coin])) $tmp_str .= PHP_EOL . '1m ago: ' . number_format($old_data['1m'][$coin], 8);
                            if (isset($old_data['5m']) and isset($old_data['5m'][$coin])) $tmp_str .= PHP_EOL . '5m ago: ' . number_format($old_data['5m'][$coin], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin])) $tmp_str .= PHP_EOL . '1h ago: ' . number_format($old_data['1h'][$coin], 8);
                            $tmp_str .= PHP_EOL . 'last price: **' . $new_price . '**';
                            $return['discord'][] = $tmp_str;
                            // break;
                        }
                    } // endforeach coin
                } // endif json_last_error
            } // endif file-exist
            else {
                // Put current coin data into array
                $arr['10s'] = $arr['1m'] = $arr['5m'] = $arr['1h'] = $list_coin;
                $arr['updated_10s'] = $arr['updated_1m'] = $arr['updated_5m'] = $arr['updated_1h'] = time();
                $arr['changed_10s'] = $arr['changed_1m'] = $arr['changed_5m'] = $arr['changed_1h'] = [];
            }
            
            // Write again into file
            $arr = json_encode($arr);
            // dump('$arr'); dump($arr);
            \BossBaby\Config::write_file($file, $arr);
            sleep(1);
        }

        return $return;
    }

    public static function get_coin_pulse_bittrex($min = -5, $max = 5)
    {
        $return = ['telegram' => [], 'discord' => []];

        // Get list current coin
        // $list_coin = \BossBaby\Bittrex::get_list_coin();
        // // \BossBaby\Utility::writeLog('list_coin:'.serialize($list_coin));
        // $list_coin = \BossBaby\Utility::object_to_array($list_coin);

        // if (!$list_coin or $list_coin['success'] != true or !$list_coin['result'])
        //     return $return;
        // $list_coin = $list_coin['result'];

        // Get list current coin
        $file = CONFIG_DIR . '/bittrex_coins.php';
        $list_coin = \BossBaby\Config::read_file($file);
        $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
        if (!json_last_error() and $list_coin)
            $list_coin = $list_coin['symbols'];
        if ($list_coin) {
            $list_coin_tmp = [];
            foreach ($list_coin as $coin => $item) {
                $list_coin_tmp[$coin] = $item['price'];
            }
            $list_coin = $list_coin_tmp;
            unset($list_coin_tmp);
        }
        if (!$list_coin) {
            $list_coin = \BossBaby\Bittrex::get_list_coin();
            if ($list_coin) {
                $list_coin_tmp = [];
                foreach ($list_coin as $pos => $item) {
                    $coin = $item['MarketName'];
                    $tmp_name = explode('-', $coin);
                    if ($tmp_name and isset($tmp_name[0]) and isset($tmp_name[1])) {
                        $coin = $tmp_name[1] . $tmp_name[0];
                    }
                    $list_coin_tmp[$coin] = $item['Last'];
                }
                $list_coin = $list_coin_tmp;
                unset($list_coin_tmp);
            }
        }
        
        // List coin should to ignore
        $list_ignore = [];

        $arr = [];
        $arr['last_updated'] = date('Y-m-d H:i:s');
        $arr['last_updated_unix'] = time();

        // File store coin data
        $file = CONFIG_DIR . '/bittrex_coins_pulse.php';

        // $min = -5; //%
        // $max = 5; //%

        // Processing on list coin
        if (is_array($list_coin) and count($list_coin)) {
            // $tmp_list = $list_coin;
            // $list_coin = [];
            // foreach ($tmp_list as $pos => $coin) {
            //     $list_coin[$coin['MarketName']] = number_format($coin['Last'], 8);
            // }
            // unset($tmp_list);

            if (is_file($file) and file_exists($file)) {
                // Read data from file
                $old_data = \BossBaby\Config::read_file($file);
                $old_data = \BossBaby\Utility::object_to_array(json_decode($old_data));
                // dump('$old_data'); dump($old_data);

                $current_time = time();
                // dump('$current_time'); dump($current_time);
                
                if (!json_last_error() and $old_data) {
                    foreach ($list_coin as $coin => $new_price) {
                        // echo '<hr/>';
                        // dump('$coin'); dump($coin);
                        if (strrpos($coin, 'BTC') === false) continue;
                        $new_price = number_format($new_price, 8);
                        // dump('$new_price'); dump($new_price);
                        if ($new_price <= 0) continue;
                        $calc_10s = $calc_1m = $calc_5m = $calc_1h = 0;
                        $coin_name = (strrpos($coin, 'BTC') !== false) ? str_replace('BTC', '', $coin) : $coin;
                        // dump('$coin_name'); dump($coin_name);
                        if (in_array($coin_name, $list_ignore)) continue;

                        // Check for 1h
                        $changed_1h = '';
                        $old_time_1h = (isset($old_data['updated_1h']) and $old_data['updated_1h']) ? $old_data['updated_1h'] : 0;
                        $changed_time_1h = $current_time - $old_time_1h;
                        // dump('changed_time_1h'); dump($changed_time_1h);
                        if ($changed_time_1h >= 60*60) {
                            $arr['1h'] = $list_coin;
                            $arr['updated_1h'] = $current_time;
                        }
                        else {
                            $arr['1h'] = $old_data['1h'];
                            $arr['updated_1h'] = $old_time_1h;
                        }
                        if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                        if (isset($old_data['changed_1h'][$coin])) {
                            $changed_1h = $arr['changed_1h'][$coin] = $old_data['changed_1h'][$coin];
                        }
                        // dump(isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h']));
                        if (isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h'])) {
                            $tmp_arr = $old_data['1h'];
                            // dump('array_key_exists($coin, $tmp_arr1h)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_1h$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_1h = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                                    $changed_1h = $arr['changed_1h'][$coin] = $calc_1h;
                                }
                            }
                        }

                        // Check for 5m
                        $changed_5m = '';
                        $old_time_5m = (isset($old_data['updated_5m']) and $old_data['updated_5m']) ? $old_data['updated_5m'] : 0;
                        $changed_time_5m = $current_time - $old_time_5m;
                        // dump('changed_time_5m'); dump($changed_time_5m);
                        if ($changed_time_5m >= 5*60) {
                            $arr['5m'] = $list_coin;
                            $arr['updated_5m'] = $current_time;
                        }
                        else {
                            $arr['5m'] = $old_data['5m'];
                            $arr['updated_5m'] = $old_time_5m;
                        }
                        if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                        if (isset($old_data['changed_5m'][$coin])) {
                            $changed_5m = $arr['changed_5m'][$coin] = $old_data['changed_5m'][$coin];
                        }
                        // dump(isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m']));
                        if (isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m'])) {
                            $tmp_arr = $old_data['5m'];
                            // dump('array_key_exists($coin, $tmp_arr5m)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_5m$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_5m = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                                    $changed_5m = $arr['changed_5m'][$coin] = $calc_5m;
                                }
                            }
                        }

                        // Check for 1m
                        $changed_1m = '';
                        $old_time_1m = (isset($old_data['updated_1m']) and $old_data['updated_1m']) ? $old_data['updated_1m'] : 0;
                        $changed_time_1m = $current_time - $old_time_1m;
                        // dump('changed_time_1m'); dump($changed_time_1m);
                        if ($changed_time_1m >= 1*60) {
                            $arr['1m'] = $list_coin;
                            $arr['updated_1m'] = $current_time;
                        }
                        else {
                            $arr['1m'] = $old_data['1m'];
                            $arr['updated_1m'] = $old_time_1m;
                        }
                        if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                        if (isset($old_data['changed_1m'][$coin])) {
                            $changed_1m = $arr['changed_1m'][$coin] = $old_data['changed_1m'][$coin];
                        }
                        // dump(isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m']));
                        if (isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m'])) {
                            $tmp_arr = $old_data['1m'];
                            // dump('array_key_exists($coin, $tmp_arr1m)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_1m$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_1m = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                                    $changed_1m = $arr['changed_1m'][$coin] = $calc_1m;
                                }
                            }
                        }

                        // Check for 10s
                        $changed_10s = '';
                        $old_time_10s = (isset($old_data['updated_10s']) and $old_data['updated_10s']) ? $old_data['updated_10s'] : 0;
                        $changed_time_10s = $current_time - $old_time_10s;
                        // dump('changed_time_10s'); dump($changed_time_10s);
                        if ($changed_time_10s >= 10) {
                            $arr['10s'] = $list_coin;
                            $arr['updated_10s'] = $current_time;
                        }
                        else {
                            $arr['10s'] = $old_data['10s'];
                            $arr['updated_10s'] = $old_time_10s;
                        }
                        if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                        if (isset($old_data['changed_10s'][$coin])) {
                            $changed_10s = $arr['changed_10s'][$coin] = $old_data['changed_10s'][$coin];
                        }
                        // dump(isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s']));
                        if (isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s'])) {
                            $tmp_arr = $old_data['10s'];
                            // dump('array_key_exists($coin, $tmp_arr10s)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_10s$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_10s = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                                    $changed_10s = $arr['changed_10s'][$coin] = $calc_10s;
                                }
                            }
                        }
                        
                        // Check to add to returns
                        if ((((($calc_10s >= 0) and ($calc_10s > $max) and (isset($old_data['changed_10s'][$coin]) and $calc_10s > $old_data['changed_10s'][$coin])) or (($calc_10s < 0) and ($calc_10s < $min) and (isset($old_data['changed_10s'][$coin]) and $calc_10s < $old_data['changed_10s'][$coin]))) and ($changed_time_10s >= 10)) 
                            or (((($calc_1m >= 0) and ($calc_1m > $max) and (isset($old_data['changed_1m'][$coin]) and $calc_1m > $old_data['changed_1m'][$coin])) or (($calc_1m < 0) and ($calc_1m < $min) and (isset($old_data['changed_1m'][$coin]) and $calc_1m < $old_data['changed_1m'][$coin]))) and ($changed_time_1m >= 1*60)) 
                            or (((($calc_5m >= 0) and ($calc_5m > $max) and (isset($old_data['changed_5m'][$coin]) and $calc_5m > $old_data['changed_5m'][$coin])) or (($calc_5m < 0) and ($calc_5m < $min) and (isset($old_data['changed_5m'][$coin]) and $calc_5m < $old_data['changed_5m'][$coin]))) and ($changed_time_5m >= 5*60)) 
                            or (((($calc_1h >= 0) and ($calc_1h > $max) and (isset($old_data['changed_1h'][$coin]) and $calc_1h > $old_data['changed_1h'][$coin])) or (($calc_1h < 0) and ($calc_1h < $min) and (isset($old_data['changed_1h'][$coin]) and $calc_1h < $old_data['changed_1h'][$coin]))) and ($changed_time_1h >= 60*60))
                        ) {
                            if ($calc_1h > $max or $calc_1h < $min) {
                                $changed_1h = ' (<b>' . (float) $calc_1h . '</b>%/1h)';
                            }
                            elseif ($calc_5m > $max or $calc_5m < $min) {
                                $changed_5m = ' (<b>' . (float) $calc_5m . '</b>%/5m)';
                            }
                            elseif ($calc_1m > $max or $calc_1m < $min) {
                                $changed_1m = ' (<b>' . (float) $calc_1m . '</b>%/1m)';
                            }
                            elseif ($calc_10s > $max or $calc_10s < $min) {
                                $changed_10s = ' (<b>' . (float) $calc_10s . '</b>%/10s)';
                            }

                            if ($changed_1h == '' and isset($old_data['changed_1h'][$coin])) $changed_1h = ' (' . (float) $old_data['changed_1h'][$coin] . '%/1h)';
                            if ($changed_5m == '' and isset($old_data['changed_5m'][$coin])) $changed_5m = ' (' . (float) $old_data['changed_5m'][$coin] . '%/5m)';
                            if ($changed_1m == '' and isset($old_data['changed_1m'][$coin])) $changed_1m = ' (' . (float) $old_data['changed_1m'][$coin] . '%/1m)';
                            if ($changed_10s == '' and isset($old_data['changed_10s'][$coin])) $changed_10s = ' (' . (float) $old_data['changed_10s'][$coin] . '%/10s)';

                            if (strpos($changed_1h, '(') === false) $changed_1h = ' (' . (float) $changed_1h . '%/1h)';
                            if (strpos($changed_5m, '(') === false) $changed_5m = ' (' . (float) $changed_5m . '%/5m)';
                            if (strpos($changed_1m, '(') === false) $changed_1m = ' (' . (float) $changed_1m . '%/1m)';
                            if (strpos($changed_10s, '(') === false) $changed_10s = ' (' . (float) $changed_10s . '%/10s)';

                            $text_out_link = \BossBaby\Utility::func_clean_double_space($changed_1h . $changed_5m . $changed_1m . $changed_10s);

                            // Format for Telegram
                            // https://www.bittrex.com/Market/Index?MarketName=BTC-USDT
                            $tmp_str = '<a href="https://bittrex.com/Market/Index?MarketName=' . $coin . '">' . $coin_name . '</a> ' . $text_out_link;
                            if (isset($old_data['10s']) and isset($old_data['10s'][$coin])) $tmp_str .= PHP_EOL . '10s ago: ' . number_format($old_data['10s'][$coin], 8);
                            if (isset($old_data['1m']) and isset($old_data['1m'][$coin])) $tmp_str .= PHP_EOL . '1m ago: ' . number_format($old_data['1m'][$coin], 8);
                            if (isset($old_data['5m']) and isset($old_data['5m'][$coin])) $tmp_str .= PHP_EOL . '5m ago: ' . number_format($old_data['5m'][$coin], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin])) $tmp_str .= PHP_EOL . '1h ago: ' . number_format($old_data['1h'][$coin], 8);
                            $tmp_str .= PHP_EOL . 'last price: <b>' . $new_price . '</b>';
                            $return['telegram'][] = $tmp_str . PHP_EOL;

                            // Format for Discord
                            $tmp_str = "\n\r" . PHP_EOL . '<https://bittrex.com/Market/Index?MarketName=' . $coin . '>';
                            $text_out_link = str_replace('<b>', '**', $text_out_link);
                            $text_out_link = str_replace('</b>', '**', $text_out_link);
                            $tmp_str .= PHP_EOL . '**#' . $coin_name . '** ' . $text_out_link;
                            if (isset($old_data['10s']) and isset($old_data['10s'][$coin])) $tmp_str .= PHP_EOL . '10s ago: ' . number_format($old_data['10s'][$coin], 8);
                            if (isset($old_data['1m']) and isset($old_data['1m'][$coin])) $tmp_str .= PHP_EOL . '1m ago: ' . number_format($old_data['1m'][$coin], 8);
                            if (isset($old_data['5m']) and isset($old_data['5m'][$coin])) $tmp_str .= PHP_EOL . '5m ago: ' . number_format($old_data['5m'][$coin], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin])) $tmp_str .= PHP_EOL . '1h ago: ' . number_format($old_data['1h'][$coin], 8);
                            $tmp_str .= PHP_EOL . 'last price: **' . $new_price . '**';
                            $return['discord'][] = $tmp_str;
                            // break;
                        }
                    } // endforeach coin
                } // endif json_last_error
            } // endif file-exist
            else {
                // Put current coin data into array
                $arr['10s'] = $arr['1m'] = $arr['5m'] = $arr['1h'] = $list_coin;
                $arr['updated_10s'] = $arr['updated_1m'] = $arr['updated_5m'] = $arr['updated_1h'] = time();
                $arr['changed_10s'] = $arr['changed_1m'] = $arr['changed_5m'] = $arr['changed_1h'] = [];
            }
            
            // Write again into file
            $arr = json_encode($arr);
            // dump('$arr'); dump($arr);
            \BossBaby\Config::write_file($file, $arr);
            sleep(1);
        }

        return $return;
    }

    public static function get_coin_pulse_houbipro($min = -5, $max = 5)
    {
        $return = ['telegram' => [], 'discord' => []];

        // Get list current coin
        $file = CONFIG_DIR . '/houbipro_coins.php';
        $list_coin = \BossBaby\Config::read_file($file);
        $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
        if (!json_last_error() and $list_coin)
            $list_coin = $list_coin['symbols'];
        if ($list_coin) {
            $list_coin_tmp = [];
            foreach ($list_coin as $coin => $item) {
                $list_coin_tmp[$coin] = $item['price'];
            }
            $list_coin = $list_coin_tmp;
            unset($list_coin_tmp);
        }
        if (!$list_coin) {
            $list_coin = \BossBaby\HoubiPro::get_list_coin();
            if ($list_coin) {
                $list_coin_tmp = [];
                foreach ($list_coin as $pos => $item) {
                    $coin = strtoupper($item['symbol']);
                    $list_coin_tmp[$coin] = $item['close'];
                }
                $list_coin = $list_coin_tmp;
                unset($list_coin_tmp);
            }
        }

        // List coin should to ignore
        $list_ignore = [];

        $arr = [];
        $arr['last_updated'] = date('Y-m-d H:i:s');
        $arr['last_updated_unix'] = time();

        // File store coin data
        $file = CONFIG_DIR . '/houbipro_coins_pulse.php';

        // $min = -5; //%
        // $max = 5; //%

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
                        // echo '<hr/>';
                        // dump('$coin'); dump($coin);
                        if (strrpos($coin, 'BTC') === false) continue;
                        $new_price = number_format($new_price, 8);
                        // dump('$new_price'); dump($new_price);
                        if ($new_price <= 0) continue;
                        $calc_10s = $calc_1m = $calc_5m = $calc_1h = 0;
                        $coin_name = (strrpos($coin, 'BTC') !== false) ? str_replace('BTC', '', $coin) : $coin;
                        // dump('$coin_name'); dump($coin_name);
                        if (in_array($coin_name, $list_ignore)) continue;

                        // Check for 1h
                        $changed_1h = '';
                        $old_time_1h = (isset($old_data['updated_1h']) and $old_data['updated_1h']) ? $old_data['updated_1h'] : 0;
                        $changed_time_1h = $current_time - $old_time_1h;
                        // dump('changed_time_1h'); dump($changed_time_1h);
                        if ($changed_time_1h >= 60*60) {
                            $arr['1h'] = $list_coin;
                            $arr['updated_1h'] = $current_time;
                        }
                        else {
                            $arr['1h'] = $old_data['1h'];
                            $arr['updated_1h'] = $old_time_1h;
                        }
                        if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                        if (isset($old_data['changed_1h'][$coin])) {
                            $changed_1h = $arr['changed_1h'][$coin] = $old_data['changed_1h'][$coin];
                        }
                        // dump(isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h']));
                        if (isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h'])) {
                            $tmp_arr = $old_data['1h'];
                            // dump('array_key_exists($coin, $tmp_arr1h)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_1h$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_1h = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                                    $changed_1h = $arr['changed_1h'][$coin] = $calc_1h;
                                }
                            }
                        }

                        // Check for 5m
                        $changed_5m = '';
                        $old_time_5m = (isset($old_data['updated_5m']) and $old_data['updated_5m']) ? $old_data['updated_5m'] : 0;
                        $changed_time_5m = $current_time - $old_time_5m;
                        // dump('changed_time_5m'); dump($changed_time_5m);
                        if ($changed_time_5m >= 5*60) {
                            $arr['5m'] = $list_coin;
                            $arr['updated_5m'] = $current_time;
                        }
                        else {
                            $arr['5m'] = $old_data['5m'];
                            $arr['updated_5m'] = $old_time_5m;
                        }
                        if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                        if (isset($old_data['changed_5m'][$coin])) {
                            $changed_5m = $arr['changed_5m'][$coin] = $old_data['changed_5m'][$coin];
                        }
                        // dump(isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m']));
                        if (isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m'])) {
                            $tmp_arr = $old_data['5m'];
                            // dump('array_key_exists($coin, $tmp_arr5m)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_5m$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_5m = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_5m'])) $arr['changed_5m'] = [];
                                    $changed_5m = $arr['changed_5m'][$coin] = $calc_5m;
                                }
                            }
                        }

                        // Check for 1m
                        $changed_1m = '';
                        $old_time_1m = (isset($old_data['updated_1m']) and $old_data['updated_1m']) ? $old_data['updated_1m'] : 0;
                        $changed_time_1m = $current_time - $old_time_1m;
                        // dump('changed_time_1m'); dump($changed_time_1m);
                        if ($changed_time_1m >= 1*60) {
                            $arr['1m'] = $list_coin;
                            $arr['updated_1m'] = $current_time;
                        }
                        else {
                            $arr['1m'] = $old_data['1m'];
                            $arr['updated_1m'] = $old_time_1m;
                        }
                        if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                        if (isset($old_data['changed_1m'][$coin])) {
                            $changed_1m = $arr['changed_1m'][$coin] = $old_data['changed_1m'][$coin];
                        }
                        // dump(isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m']));
                        if (isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m'])) {
                            $tmp_arr = $old_data['1m'];
                            // dump('array_key_exists($coin, $tmp_arr1m)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_1m$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_1m = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_1m'])) $arr['changed_1m'] = [];
                                    $changed_1m = $arr['changed_1m'][$coin] = $calc_1m;
                                }
                            }
                        }

                        // Check for 10s
                        $changed_10s = '';
                        $old_time_10s = (isset($old_data['updated_10s']) and $old_data['updated_10s']) ? $old_data['updated_10s'] : 0;
                        $changed_time_10s = $current_time - $old_time_10s;
                        // dump('changed_time_10s'); dump($changed_time_10s);
                        if ($changed_time_10s >= 10) {
                            $arr['10s'] = $list_coin;
                            $arr['updated_10s'] = $current_time;
                        }
                        else {
                            $arr['10s'] = $old_data['10s'];
                            $arr['updated_10s'] = $old_time_10s;
                        }
                        if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                        if (isset($old_data['changed_10s'][$coin])) {
                            $changed_10s = $arr['changed_10s'][$coin] = $old_data['changed_10s'][$coin];
                        }
                        // dump(isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s']));
                        if (isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s'])) {
                            $tmp_arr = $old_data['10s'];
                            // dump('array_key_exists($coin, $tmp_arr10s)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin], 8);
                                // dump('changed_10s$old_price'); dump($old_price);
                                if ($old_price > 0) {
                                    $calc_10s = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_10s'])) $arr['changed_10s'] = [];
                                    $changed_10s = $arr['changed_10s'][$coin] = $calc_10s;
                                }
                            }
                        }
                        
                        // Check to add to returns
                        if ((((($calc_10s >= 0) and ($calc_10s > $max) and (isset($old_data['changed_10s'][$coin]) and $calc_10s > $old_data['changed_10s'][$coin])) or (($calc_10s < 0) and ($calc_10s < $min) and (isset($old_data['changed_10s'][$coin]) and $calc_10s < $old_data['changed_10s'][$coin]))) and ($changed_time_10s >= 10)) 
                            or (((($calc_1m >= 0) and ($calc_1m > $max) and (isset($old_data['changed_1m'][$coin]) and $calc_1m > $old_data['changed_1m'][$coin])) or (($calc_1m < 0) and ($calc_1m < $min) and (isset($old_data['changed_1m'][$coin]) and $calc_1m < $old_data['changed_1m'][$coin]))) and ($changed_time_1m >= 1*60)) 
                            or (((($calc_5m >= 0) and ($calc_5m > $max) and (isset($old_data['changed_5m'][$coin]) and $calc_5m > $old_data['changed_5m'][$coin])) or (($calc_5m < 0) and ($calc_5m < $min) and (isset($old_data['changed_5m'][$coin]) and $calc_5m < $old_data['changed_5m'][$coin]))) and ($changed_time_5m >= 5*60)) 
                            or (((($calc_1h >= 0) and ($calc_1h > $max) and (isset($old_data['changed_1h'][$coin]) and $calc_1h > $old_data['changed_1h'][$coin])) or (($calc_1h < 0) and ($calc_1h < $min) and (isset($old_data['changed_1h'][$coin]) and $calc_1h < $old_data['changed_1h'][$coin]))) and ($changed_time_1h >= 60*60))
                        ) {
                            if ($calc_1h > $max or $calc_1h < $min) {
                                $changed_1h = ' (<b>' . (float) $calc_1h . '</b>%/1h)';
                            }
                            elseif ($calc_5m > $max or $calc_5m < $min) {
                                $changed_5m = ' (<b>' . (float) $calc_5m . '</b>%/5m)';
                            }
                            elseif ($calc_1m > $max or $calc_1m < $min) {
                                $changed_1m = ' (<b>' . (float) $calc_1m . '</b>%/1m)';
                            }
                            elseif ($calc_10s > $max or $calc_10s < $min) {
                                $changed_10s = ' (<b>' . (float) $calc_10s . '</b>%/10s)';
                            }
                            
                            if ($changed_1h == '' and isset($old_data['changed_1h'][$coin])) $changed_1h = ' (' . (float) $old_data['changed_1h'][$coin] . '%/1h)';
                            if ($changed_5m == '' and isset($old_data['changed_5m'][$coin])) $changed_5m = ' (' . (float) $old_data['changed_5m'][$coin] . '%/5m)';
                            if ($changed_1m == '' and isset($old_data['changed_1m'][$coin])) $changed_1m = ' (' . (float) $old_data['changed_1m'][$coin] . '%/1m)';
                            if ($changed_10s == '' and isset($old_data['changed_10s'][$coin])) $changed_10s = ' (' . (float) $old_data['changed_10s'][$coin] . '%/10s)';

                            if (strpos($changed_1h, '(') === false) $changed_1h = ' (' . (float) $changed_1h . '%/1h)';
                            if (strpos($changed_5m, '(') === false) $changed_5m = ' (' . (float) $changed_5m . '%/5m)';
                            if (strpos($changed_1m, '(') === false) $changed_1m = ' (' . (float) $changed_1m . '%/1m)';
                            if (strpos($changed_10s, '(') === false) $changed_10s = ' (' . (float) $changed_10s . '%/10s)';

                            $text_out_link = \BossBaby\Utility::func_clean_double_space($changed_1h . $changed_5m . $changed_1m . $changed_10s);

                            // Format for Telegram
                            // https://www.huobi.com/ltc_btc/exchange/
                            $tmp_str = '<a href="https://www.huobi.com/' . strtolower($coin_name) . '_btc/exchange/">' . $coin_name . '</a> ' . $text_out_link;
                            if (isset($old_data['10s']) and isset($old_data['10s'][$coin])) $tmp_str .= PHP_EOL . '10s ago: ' . number_format($old_data['10s'][$coin], 8);
                            if (isset($old_data['1m']) and isset($old_data['1m'][$coin])) $tmp_str .= PHP_EOL . '1m ago: ' . number_format($old_data['1m'][$coin], 8);
                            if (isset($old_data['5m']) and isset($old_data['5m'][$coin])) $tmp_str .= PHP_EOL . '5m ago: ' . number_format($old_data['5m'][$coin], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin])) $tmp_str .= PHP_EOL . '1h ago: ' . number_format($old_data['1h'][$coin], 8);
                            $tmp_str .= PHP_EOL . 'last price: <b>' . $new_price . '</b>';
                            $return['telegram'][] = $tmp_str . PHP_EOL;

                            // Format for Discord
                            $tmp_str = "\n\r" . PHP_EOL . '<https://www.huobi.com/' . strtolower($coin_name) . '_btc/exchange/>';
                            $text_out_link = str_replace('<b>', '**', $text_out_link);
                            $text_out_link = str_replace('</b>', '**', $text_out_link);
                            $tmp_str .= PHP_EOL . '**#' . $coin_name . '** ' . $text_out_link;
                            if (isset($old_data['10s']) and isset($old_data['10s'][$coin])) $tmp_str .= PHP_EOL . '10s ago: ' . number_format($old_data['10s'][$coin], 8);
                            if (isset($old_data['1m']) and isset($old_data['1m'][$coin])) $tmp_str .= PHP_EOL . '1m ago: ' . number_format($old_data['1m'][$coin], 8);
                            if (isset($old_data['5m']) and isset($old_data['5m'][$coin])) $tmp_str .= PHP_EOL . '5m ago: ' . number_format($old_data['5m'][$coin], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin])) $tmp_str .= PHP_EOL . '1h ago: ' . number_format($old_data['1h'][$coin], 8);
                            $tmp_str .= PHP_EOL . 'last price: **' . $new_price . '**';
                            $return['discord'][] = $tmp_str;
                            // break;
                        }
                    } // endforeach coin
                } // endif json_last_error
            } // endif file-exist
            else {
                // Put current coin data into array
                $arr['10s'] = $arr['1m'] = $arr['5m'] = $arr['1h'] = $list_coin;
                $arr['updated_10s'] = $arr['updated_1m'] = $arr['updated_5m'] = $arr['updated_1h'] = time();
                $arr['changed_10s'] = $arr['changed_1m'] = $arr['changed_5m'] = $arr['changed_1h'] = [];
            }
            
            // Write again into file
            $arr = json_encode($arr);
            // dump('$arr'); dump($arr);
            \BossBaby\Config::write_file($file, $arr);
            sleep(1);
        }

        return $return;
    }

    public static function get_twitter_feeds($username = '', $count = 1)
    {
        if (!$username or !$count) return [];

        $tmp = \BossBaby\Twitter::get_user_feeds($username, $count);

        if (is_object($tmp))
            $tmp = \BossBaby\Utility::object_to_array($tmp);

        if (isset($tmp['errors'])) {
            // throw new Exception($tmp['errors'][0]['message']);
            \BossBaby\Utility::writeLog(__FILE__.'::error:'.$tmp['errors'][0]['message']);
        }

        // dump($tmp);

        $arr = [];
        foreach ($tmp as $pos => $status) {
            // $status = \BossBaby\Utility::array_to_object($status);
            // $arr[] = \Twitter::clickable($status);
            
            // if (!property_exists('stdClass', 'text')) {
            //     \BossBaby\Utility::writeLog(__FILE__.'::error:'.serialize($status));
            //     continue;
            // }

            // $arr[$status->id] = (isset($status->text)) ? $status->text : '';

            $status = \BossBaby\Utility::object_to_array($status);
            if (isset($status['id']) and isset($status['text']))
                $arr[$status['id']] = (isset($status['text'])) ? $status['text'] : '';
        }
        
        return $arr;
    }

    public static function get_binance_balances()
    {
        $tmp = \BossBaby\Binance::get_balances();
        
        $arr = [];
        if (!$tmp) return $arr;

        foreach ($tmp as $coin => $data) {
            if ($data['available'] > 0 or $data['onOrder'] > 0 or $data['btcValue'] > 0)
                $arr[$coin] = $data;
        }

        return $arr;
    }

    public static function get_bittrex_balances()
    {
        $tmp = \BossBaby\Bittrex::get_balances();
        
        $arr = [];
        if (!$tmp or $tmp->success != 1) return $arr;
        $tmp = $tmp->result;
        $tmp = \BossBaby\Utility::object_to_array($tmp);

        foreach ($tmp as $pos => $data) {
            $coin = $data['Currency'];
            $data['available'] = $data['Balance'];
            $data['onOrder'] = 0;
            $data['btcValue'] = 0;
            if ($data['available'] > 0 or $data['onOrder'] > 0 or $data['btcValue'] > 0)
                $arr[$coin] = $data;
        }

        return $arr;
    }

    public static function get_coin_vol_binance($min = -5, $max = 5, $min_vol = -5, $max_vol = 5)
    {
        // \BossBaby\Utility::writeLog('----'.__FILE__ . '::' . __FUNCTION__ . '::min::' . $min . '::max::' . $max . '::min_vol::' . $min_vol . '::max_vol::' . $max_vol);

        $return = ['telegram' => [], 'discord' => []];

        // Get list current coin
        $file = CONFIG_DIR . '/binance_coins.php';
        $list_coin = \BossBaby\Config::read_file($file);
        $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
        if (!json_last_error() and $list_coin)
            $list_coin = $list_coin['symbols'];
        if (!$list_coin) {
            $list_coin = \BossBaby\Binance::get_ticker_24h();
        }
        if ($list_coin) {
            $list_coin_tmp = [];
            foreach ($list_coin as $key => $item) {
                $coin = $item['symbol'];
                if (strrpos($coin, 'BTC') === false) continue;
                // $list_coin_tmp[$coin] = $item;
                $list_coin_tmp[$coin]['price'] = $item['lastPrice'];
                $list_coin_tmp[$coin]['volume'] = $item['volume'];
            }
            $list_coin = $list_coin_tmp;
            unset($list_coin_tmp);
        }
        // dump('$list_coin');dump($list_coin);die;
        
        // List coin should to ignore
        $list_ignore = [];

        $arr = [];
        $arr['last_updated'] = date('Y-m-d H:i:s');
        $arr['last_updated_unix'] = time();

        // File store coin data
        $file = CONFIG_DIR . '/binance_coins_vol.php';

        // $min = -5; //%
        // $max = 5; //%

        // Processing on list coin
        if (is_array($list_coin) and count($list_coin)) {
            if (is_file($file) and file_exists($file)) {
                // Read data from file
                $old_data = \BossBaby\Config::read_file($file);
                $old_data = \BossBaby\Utility::object_to_array(json_decode($old_data));
                // dump('$old_data'); dump($old_data);die;

                $current_time = time();
                // dump('$current_time'); dump($current_time);
                
                if (!json_last_error() and $old_data) {
                    foreach ($list_coin as $coin => $item) {
                        // echo '<hr/>';
                        // dump('$coin'); dump($coin);
                        if (strrpos($coin, 'BTC') === false) continue;
                        $new_price = number_format($item['price'], 8);
                        $new_volume = $item['volume'];
                        // dump('$new_price'); dump($new_price);
                        // dump('$new_volume'); dump($new_volume);
                        if ($new_price <= 0 or $new_volume <= 0) continue;
                        $calc_15m = $calc_1h = 0;
                        $calc_vol_15m = $calc_vol_1h = 0;
                        $coin_name = (strrpos($coin, 'BTC') !== false) ? str_replace('BTC', '', $coin) : $coin;
                        // dump('$coin_name'); dump($coin_name); die;
                        if (in_array($coin_name, $list_ignore)) continue;

                        // Check for 1h
                        $changed_1h = '';
                        $old_time_1h = (isset($old_data['updated_1h']) and $old_data['updated_1h']) ? $old_data['updated_1h'] : 0;
                        $changed_time_1h = $current_time - $old_time_1h;
                        // dump('changed_time_1h'); dump($changed_time_1h);
                        if ($changed_time_1h >= 60*60) {
                            $arr['1h'] = $list_coin;
                            $arr['updated_1h'] = $current_time;
                        }
                        else {
                            $arr['1h'] = $old_data['1h'];
                            $arr['updated_1h'] = $old_time_1h;
                        }
                        if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                        if (isset($old_data['changed_1h'][$coin])) {
                            $changed_1h = $arr['changed_1h'][$coin] = $old_data['changed_1h'][$coin];
                        }
                        if (!isset($arr['changed_vol_1h'])) $arr['changed_vol_1h'] = [];
                        if (isset($old_data['changed_vol_1h'][$coin])) {
                            $changed_vol_1h = $arr['changed_vol_1h'][$coin] = $old_data['changed_vol_1h'][$coin];
                        }
                        // dump(isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h']));
                        if (isset($old_data['1h']) and is_array($old_data['1h']) and count($old_data['1h'])) {
                            $tmp_arr = $old_data['1h'];
                            // dump('array_key_exists($coin, $tmp_arr1h)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin]['price'], 8);
                                $old_volume = $tmp_arr[$coin]['volume'];
                                // dump('changed_1h$old_price'); dump($old_price);
                                // dump('changed_1h$old_volume'); dump($old_volume);
                                if ($old_price > 0 and $old_volume > 0) {
                                    $calc_1h = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_1h'])) $arr['changed_1h'] = [];
                                    $changed_1h = $arr['changed_1h'][$coin] = $calc_1h;
                                    $calc_vol_1h = round((($new_volume - $old_volume) / $old_volume) * 100, 2);
                                    if (!isset($arr['changed_vol_1h'])) $arr['changed_vol_1h'] = [];
                                    $changed_vol_1h = $arr['changed_vol_1h'][$coin] = $calc_vol_1h;
                                }
                            }
                        }

                        // Check for 15m
                        $changed_15m = '';
                        $old_time_15m = (isset($old_data['updated_15m']) and $old_data['updated_15m']) ? $old_data['updated_15m'] : 0;
                        $changed_time_15m = $current_time - $old_time_15m;
                        // dump('changed_time_15m'); dump($changed_time_15m);
                        if ($changed_time_15m >= 15*60) {
                            $arr['15m'] = $list_coin;
                            $arr['updated_15m'] = $current_time;
                        }
                        else {
                            $arr['15m'] = $old_data['15m'];
                            $arr['updated_15m'] = $old_time_15m;
                        }
                        if (!isset($arr['changed_15m'])) $arr['changed_15m'] = [];
                        if (isset($old_data['changed_15m'][$coin])) {
                            $changed_15m = $arr['changed_15m'][$coin] = $old_data['changed_15m'][$coin];
                        }
                        if (!isset($arr['changed_vol_15m'])) $arr['changed_vol_15m'] = [];
                        if (isset($old_data['changed_vol_15m'][$coin])) {
                            $changed_vol_15m = $arr['changed_vol_15m'][$coin] = $old_data['changed_vol_15m'][$coin];
                        }
                        // dump(isset($old_data['15m']) and is_array($old_data['15m']) and count($old_data['15m']));
                        if (isset($old_data['15m']) and is_array($old_data['15m']) and count($old_data['15m'])) {
                            $tmp_arr = $old_data['15m'];
                            // dump('array_key_exists($coin, $tmp_arr15m)'); dump(array_key_exists($coin, $tmp_arr));
                            if (array_key_exists($coin, $tmp_arr)) {
                                $old_price = number_format($tmp_arr[$coin]['price'], 8);
                                $old_volume = $tmp_arr[$coin]['volume'];
                                // dump('changed_15m$old_price'); dump($old_price);
                                // dump('changed_15m$old_volume'); dump($old_volume);
                                if ($old_price > 0 and $old_volume > 0) {
                                    $calc_15m = round((($new_price - $old_price) / $old_price) * 100, 2);
                                    if (!isset($arr['changed_15m'])) $arr['changed_15m'] = [];
                                    $changed_15m = $arr['changed_15m'][$coin] = $calc_15m;
                                    $calc_vol_15m = round((($new_volume - $old_volume) / $old_volume) * 100, 2);
                                    if (!isset($arr['changed_vol_15m'])) $arr['changed_vol_15m'] = [];
                                    $changed_vol_15m = $arr['changed_vol_15m'][$coin] = $calc_vol_15m;
                                }
                            }
                        }

                        // Check to add to returns
                        if (((($calc_15m <= $max) and ($calc_vol_15m > $max_vol) and (isset($old_data['changed_vol_15m'][$coin]) and $calc_15m > $old_data['changed_vol_15m'][$coin])) and ($changed_time_15m >= 15*60))
                            or ((($calc_1h <= $max) and ($calc_vol_1h > $max_vol) and (isset($old_data['changed_vol_1h'][$coin]) and $calc_1h > $old_data['changed_vol_1h'][$coin])) and ($changed_time_1h >= 60*60))
                        ) {
                            // if ($calc_1h <= $max)
                            //     $changed_1h = ' (<b>' . (float) $calc_1h . '</b>%/1h)';
                            // elseif ($calc_15m <= $max)
                            //     $changed_15m = ' (<b>' . (float) $calc_15m . '</b>%/15m)';
                            
                            // if ($changed_1h == '' and isset($old_data['changed_1h'][$coin])) $changed_1h = ' (' . (float) $old_data['changed_1h'][$coin] . '%/1h)';
                            // if ($changed_15m == '' and isset($old_data['changed_15m'][$coin])) $changed_15m = ' (' . (float) $old_data['changed_15m'][$coin] . '%/15m)';
                            // if (strpos($changed_1h, '(') === false) $changed_1h = ' (' . $changed_1h . '%/1h)';
                            // if (strpos($changed_15m, '(') === false) $changed_15m = ' (' . $changed_15m . '%/15m)';

                            if ($calc_vol_1h > $max_vol)
                                $changed_vol_1h = ' (<b>' . (float) $calc_vol_1h . '</b>%/1h)';
                            elseif ($calc_vol_15m > $max_vol)
                                $changed_vol_15m = ' (<b>' . (float) $calc_vol_15m . '</b>%/15m)';
                            
                            if ($changed_vol_1h == '' and isset($old_data['changed_vol_1h'][$coin])) $changed_vol_1h = ' (' . (float) $old_data['changed_vol_1h'][$coin] . '%/1h)';
                            if ($changed_vol_15m == '' and isset($old_data['changed_vol_15m'][$coin])) $changed_vol_15m = ' (' . (float) $old_data['changed_vol_15m'][$coin] . '%/15m)';
                            if (strpos($changed_vol_1h, '(') === false) $changed_vol_1h = ' (' . $changed_vol_1h . '%/1h)';
                            if (strpos($changed_vol_15m, '(') === false) $changed_vol_15m = ' (' . $changed_vol_15m . '%/15m)';
                            
                            $text_out_link = \BossBaby\Utility::func_clean_double_space($changed_vol_1h . $changed_vol_15m);

                            // Format for Telegram
                            // https://www.binance.com/trade.html?symbol=BTC_USDT
                            $tmp_str = '<a href="https://www.binance.com/trade.html?symbol=' . $coin_name . '_BTC">' . $coin_name . '</a> ' . $text_out_link . PHP_EOL;
                            if (isset($old_data['15m']) and isset($old_data['15m'][$coin]['price'])) $tmp_str .= '15m ago: ' . number_format($old_data['15m'][$coin]['price'], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin]['price'])) $tmp_str .= ' -1h ago: ' . number_format($old_data['1h'][$coin]['price'], 8);
                            $tmp_str .= PHP_EOL . 'last price: <b>' . $new_price . '</b>' . PHP_EOL;
                            if (isset($old_data['15m']) and isset($old_data['15m'][$coin]['volume'])) $tmp_str .= '15m ago: ' . number_format($old_data['15m'][$coin]['volume'], 2);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin]['volume'])) $tmp_str .= ' -1h ago: ' . number_format($old_data['1h'][$coin]['volume'], 2);
                            $tmp_str .= PHP_EOL . 'last volume: <b>' . number_format($new_volume, 2) . '</b>';
                            $return['telegram'][] = $tmp_str . PHP_EOL;

                            // Format for Discord
                            $tmp_str = "\n\r" . PHP_EOL . '<https://www.binance.com/trade.html?symbol=' . $coin_name . '_BTC>';
                            $text_out_link = str_replace('<b>', '**', $text_out_link);
                            $text_out_link = str_replace('</b>', '**', $text_out_link);
                            $tmp_str .= PHP_EOL . '**#' . $coin_name . '** ' . $text_out_link . PHP_EOL;
                            if (isset($old_data['15m']) and isset($old_data['1m'][$coin]['price'])) $tmp_str .= '15m ago: ' . number_format($old_data['15m'][$coin]['price'], 8);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin]['price'])) $tmp_str .= ' -1h ago: ' . number_format($old_data['1h'][$coin]['price'], 8);
                            $tmp_str .= PHP_EOL . 'last price: **' . $new_price . '**' . PHP_EOL;
                            if (isset($old_data['15m']) and isset($old_data['15m'][$coin]['volume'])) $tmp_str .= '15m ago: ' . number_format($old_data['15m'][$coin]['volume'], 2);
                            if (isset($old_data['1h']) and isset($old_data['1h'][$coin]['volume'])) $tmp_str .= ' -1h ago: ' . number_format($old_data['1h'][$coin]['volume'], 2);
                            $tmp_str .= PHP_EOL . 'last volume: **' . number_format($new_volume, 2) . '**';
                            $return['discord'][] = $tmp_str;
                            // break;
                        }
                    } // endforeach coin
                } // endif json_last_error
            } // endif file-exist
            else {
                // Put current coin data into array
                $arr['15m'] = $arr['1h'] = $list_coin;
                $arr['updated_15m'] = $arr['updated_1h'] = time();
                $arr['changed_15m'] = $arr['changed_1h'] = [];
                $arr['changed_vol_15m'] = $arr['changed_vol_1h'] = [];
            }
            
            // Write again into file
            $arr = json_encode($arr);
            // dump('$arr'); dump($arr);
            \BossBaby\Config::write_file($file, $arr);
            sleep(1);
        }

        return $return;
    }

    public static function clean_command($command = '')
    {
        $command = trim($command);
        $command = ltrim($command, '/');
        // $command = str_replace('/', '', $command);
        return preg_replace('!\s+!', ' ', $command);
    }
}