<?php
namespace BossBaby;

class Bittrex
{
    public static function get_coin_price($binance = null, $coin = null)
    {
        $arr = $binance->prices();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin));

        if ($arr and $coin) {
            $arr_tmp = [];
            foreach ($arr as $key => $value) {
                if (strpos($key, $coin . 'BTC') !== false or strpos($key, $coin . 'ETH') !== false or strpos($key, $coin . 'USDT') !== false or strpos($key, $coin . 'BNB') !== false)
                    $arr_tmp[$key] = $value;
            }
            // \BossBaby\Utility::writeLog('arr_tmp:'.serialize($arr_tmp).PHP_EOL.'-coin:'.serialize($coin));
            if ($arr_tmp) return $arr_tmp;
        }

        // return $arr;
        return null;
    }
}