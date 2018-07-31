<?php
namespace BossBaby;

class Binance
{
    public static function get_coin_price($coin = null)
    {
        global $environment;
        $environment->binance_instance = new \Binance($environment->binance->{1}->apiKey, $environment->bitmex->{1}->apiSecret);

        if (!is_object($environment->binance_instance) or !$coin) return [];

        $tmp = $environment->binance_instance->prices();
        // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));

        $arr = [];
        if (is_array($tmp) and count($tmp)) {
            foreach ($tmp as $key => $value) {
                if (strpos($key, $coin . 'BTC') !== false or strpos($key, $coin . 'ETH') !== false or strpos($key, $coin . 'USDT') !== false or strpos($key, $coin . 'BNB') !== false)
                    $arr[$key] = $value;
            }
        }

        // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin));
        return $arr;
    }

    public static function get_list_coin()
    {
        global $environment;
        $environment->binance_instance = new \Binance($environment->binance->{1}->apiKey, $environment->bitmex->{1}->apiSecret);
        
        if (!is_object($environment->binance_instance)) return [];

        $arr = $environment->binance_instance->prices();
        // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));

        return $arr;
    }
}