<?php
namespace BossBaby;

class HitBTC
{
    public static function get_coin_price($coin = null)
    {
        if (!$coin) return [];

        // global $environment;
        // $environment->hitbtc_instance = new \Binance($environment->hitbtc->accounts->{1}->apiKey, $environment->hitbtc->accounts->{1}->apiSecret);
        // if (!is_object($environment->hitbtc_instance)) return [];

        // $tmp = $environment->hitbtc_instance->prices();
        $tmp = \BossBaby\Binance::get_list_coin();
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
        $environment->hitbtc_instance = new \Binance($environment->hitbtc->accounts->{1}->apiKey, $environment->hitbtc->accounts->{1}->apiSecret);
        if (!is_object($environment->hitbtc_instance)) return [];

        $arr = $environment->hitbtc_instance->prices();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        return $arr;
    }

    public static function get_balances()
    {
        global $environment;
        $environment->hitbtc_instance = new \Binance($environment->hitbtc->accounts->{1}->apiKey, $environment->hitbtc->accounts->{1}->apiSecret);
        if (!is_object($environment->hitbtc_instance)) return [];

        $arr = $environment->hitbtc_instance->balances();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        return $arr;
    }

    public static function get_candlesticks($symbol, $interval = "5m", $limit = 1, $startTime = '', $endTime = '')
    {
        global $environment;
        $environment->hitbtc_instance = new \Binance($environment->hitbtc->accounts->{1}->apiKey, $environment->hitbtc->accounts->{1}->apiSecret);
        if (!is_object($environment->hitbtc_instance)) return [];

        $arr = $environment->hitbtc_instance->candlesticks($symbol, $interval, $limit, $startTime, $endTime);
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        // [
        //     [
        //         1499040000000,      // Open time
        //         "0.01634790",       // Open
        //         "0.80000000",       // High
        //         "0.01575800",       // Low
        //         "0.01577100",       // Close
        //         "148976.11427815",  // Volume
        //         1499644799999,      // Close time
        //         "2434.19055334",    // Quote asset volume
        //         308,                // Number of trades
        //         "1756.87402397",    // Taker buy base asset volume
        //         "28.46694368",      // Taker buy quote asset volume
        //         "17928899.62484339" // Ignore.
        //     ]
        // ]

        return $arr;
    }
}