<?php
namespace BossBaby;

class Binance
{
    public static function get_coin_price($binance = null, $coin = null)
    {
        if (!is_object($binance) or !$coin) return [];

        $tmp = $binance->prices();
        // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp).PHP_EOL.'-coin:'.serialize($coin));

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
}