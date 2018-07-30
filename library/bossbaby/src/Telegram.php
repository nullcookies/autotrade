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
        global $environment;
        $price = '';

        $environment->bitmex_instance = new \Bitmex($environment->bitmex->{1}->apiKey, $environment->bitmex->{1}->apiSecret);
        $arr = \BossBaby\Bitmex::func_get_current_price($environment->bitmex_instance);

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
        global $environment;
        $price = '';
        $coin_name = strtoupper($coin_name);
        
        $environment->binance_instance = new \Binance($environment->binance->{1}->apiKey, $environment->bitmex->{1}->apiSecret);
        $arr = \BossBaby\Binance::get_coin_price($environment->binance_instance, $coin_name);

        if ($arr) {
            // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin_name));
            // $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $price .= 'Giá *' . $coin_name . '* trên Binance:' . PHP_EOL;
            foreach ($arr as $key => $value) {
                $price .= str_replace($coin_name, $coin_name . '/', $key) . ': ' . $value . PHP_EOL;
            }
        }

        $environment->bittrex_instance = new \Bittrex($environment->bittrex->{1}->apiKey, $environment->bittrex->{1}->apiSecret);
        $arr = \BossBaby\Bittrex::get_coin_price($environment->bittrex_instance, $coin_name);

        if ($arr) {
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
}