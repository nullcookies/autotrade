<?php
namespace BossBaby;

class Binance
{
    public static function get_coin_price($binance = null, $coin = null)
    {
        if (!is_object($binance) or !$coin) return [];

        $tmp = $binance->prices();
        // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));

        $arr = [];
        if ($tmp) {
            foreach ($tmp as $key => $value) {
                if (strpos($key, $coin . 'BTC') !== false or strpos($key, $coin . 'ETH') !== false or strpos($key, $coin . 'USDT') !== false or strpos($key, $coin . 'BNB') !== false)
                    $arr[$key] = $value;
            }
        }

        // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin));
        return $arr;
    }

    public static function get_list_coin($binance = null)
    {
        if (!is_object($binance)) return [];

        $arr = $binance->prices();
        // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));

        return $arr;
    }
}