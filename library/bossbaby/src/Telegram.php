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

    public static function get_coin_pulse()
    {
        // Get list current coin
        $list_coin = \BossBaby\Binance::get_list_coin();
        // \BossBaby\Utility::writeLog('list_coin:'.serialize($list_coin));

        // List coin should ignore
        $list_ignore = [];

        $arr = [];
        $arr['last_updated'] = date('Y-m-d H:i:s');
        $arr['last_updated_unix'] = time();

        // File store coin data
        $file = LOGS_DIR . '/binance_coins.php';
        // \BossBaby\Utility::writeLog('file:'.serialize($file));

        $return = '';
        $min = -1; //%
        $max = 1; //%

        // Processing on list coin
        if (is_array($list_coin) and count($list_coin)) {
            if (is_file($file) and file_exists($file)) {
                // Read data from file
                $old_data = \BossBaby\Config::read_file($file);
                $old_data = (array) json_decode($old_data);
                // dump('$old_data'); dump($old_data);

                if (json_last_error() or !$old_data) {
                    $arr['10s'] = $arr['1m'] = $arr['5m'] = $list_coin;
                }
                else {
                    foreach ($list_coin as $coin => $new_price) {
                        // dump('$new_price'); dump($new_price);
                        if (strrpos($coin, 'BTC') === false) continue;
                        $calc_10s = $calc_1m = $calc_5m = 0;
                        $coin_name = (strrpos($coin, 'BTC') !== false) ? str_replace('BTC', '', $coin) : $coin;
                        // dump('$coin'); dump($coin);
                        // dump('$coin_name'); dump($coin_name);

                        // Check for 10s
                        $changed_10s = '';
                        if (isset($old_data['last_updated_unix']) and (int) $old_data['last_updated_unix'] and (time() - $old_data['last_updated_unix']) >= 10) {
                            $arr['10s'] = $list_coin;
                            if (isset($old_data['10s']) and is_array($old_data['10s']) and count($old_data['10s'])) {
                                $tmp_arr = $old_data['10s'];
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_10s = round($cal, 2);
                                    if ($calc_10s > $max or $calc_10s < $min)
                                        $changed_10s .= ' (<b>' . $calc_10s . '</b>%/10s)';
                                    else
                                        $changed_10s .= ' (' . $calc_10s . '%/10s)';
                                }
                            }
                        }
                        else {
                            $arr['10s'] = $old_data['10s'];
                        }

                        // Check for 1m
                        $changed_1m = '';
                        if (isset($old_data['last_updated_unix']) and (int) $old_data['last_updated_unix'] and (time() - $old_data['last_updated_unix']) >= (1*60)) {
                            $arr['1m'] = $list_coin;
                            if (isset($old_data['1m']) and is_array($old_data['1m']) and count($old_data['1m'])) {
                                $tmp_arr = $old_data['1m'];
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_1m = round($cal, 2);
                                    if ($calc_1m > $max or $calc_1m < $min)
                                        $changed_1m .= ' (<b>' . $calc_1m . '</b>%/1m)';
                                    else
                                        $changed_1m .= ' (' . $calc_1m . '%/1m)';
                                }
                            }
                        }
                        else {
                            $arr['1m'] = $old_data['1m'];
                        }

                        // Check for 5m
                        $changed_5m = '';
                        if (isset($old_data['last_updated_unix']) and (int) $old_data['last_updated_unix'] and (time() - $old_data['last_updated_unix']) >= (5*60)) {
                            $arr['5m'] = $list_coin;
                            if (isset($old_data['5m']) and is_array($old_data['5m']) and count($old_data['5m'])) {
                                $tmp_arr = $old_data['5m'];
                                if (array_key_exists($coin, $tmp_arr)) {
                                    $old_price = $tmp_arr[$coin];
                                    $cal = (($new_price - $old_price) / $old_price) * 100;
                                    $calc_5m = round($cal, 2);
                                    if ($calc_5m > $max or $calc_5m < $min)
                                        $changed_5m .= ' (<b>' . $calc_5m . '</b>%/5m)';
                                    else
                                        $changed_5m .= ' (' . $calc_5m . '%/5m)';
                                }
                            }
                        }
                        else {
                            $arr['5m'] = $old_data['5m'];
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
                            $text_out_link = \BossBaby\Utility::func_clean_double_space($changed_10s . $changed_1m . $changed_5m);
                            $return .= PHP_EOL . '<a href="https://www.binance.com/trade.html?symbol=' . $coin_name . '_BTC">' . $coin_name . '</a> ' . $text_out_link;
                        }
                        // break;
                    }
                }

                if (!isset($old_data['10s']) or !is_array($old_data['10s'])) $arr['10s'] = $list_coin;
                if (!isset($old_data['1m']) or !is_array($old_data['1m'])) $arr['1m'] = $list_coin;
                if (!isset($old_data['5m']) or !is_array($old_data['5m'])) $arr['5m'] = $list_coin;
            }
            else {
                // Put current coin data into array
                $arr['10s'] = $arr['1m'] = $arr['5m'] = $list_coin;
            }
            
            // Write again into file
            $arr = json_encode($arr);
            // dump('$arr'); dump($arr);
            \BossBaby\Config::write_file($file, $arr);

            $return .= PHP_EOL;
        }

        // dump('$return'); dump($return);die;
        return $return;
    }
}