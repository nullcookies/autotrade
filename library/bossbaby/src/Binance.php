<?php
namespace BossBaby;

class Binance
{
    public static function get_coin_price($coin_name = null)
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::coin:'.serialize($coin_name));
        
        if (!$coin_name) return [];

        // global $environment;
        // $environment->binance_instance = new \Binance($environment->binance->accounts->{1}->apiKey, $environment->binance->accounts->{1}->apiSecret);
        // if (!is_object($environment->binance_instance)) return [];

        // $list_coin = $environment->binance_instance->prices();
        // $list_coin = \BossBaby\Binance::get_list_coin();

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
        if (!$list_coin) {
            $list_coin = \BossBaby\Binance::get_list_coin();
        }

        // \BossBaby\Utility::writeLog('coin:'.serialize($coin_name).PHP_EOL.'-list_coin:'.serialize($list_coin));

        $arr = [];
        $arr_retrieve = [$coin_name . 'BTC', $coin_name . 'ETH', $coin_name . 'USDT', $coin_name . 'USDT', $coin_name . 'BNB', $coin_name . 'HT'];
        if (is_array($list_coin) and count($list_coin)) {
            foreach ($list_coin as $symbol => $price) {
                if (in_array($symbol, $arr_retrieve))
                    $arr[$symbol] = number_format($price, 8);
            }
        }

        return $arr;
    }

    public static function get_list_coin()
    {
        global $environment;
        $environment->binance_instance = new \Binance($environment->binance->accounts->{1}->apiKey, $environment->binance->accounts->{1}->apiSecret);
        if (!is_object($environment->binance_instance)) return [];

        $arr = $environment->binance_instance->prices();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        return $arr;
    }

    public static function get_ticker_24h()
    {
        global $environment;
        $environment->binance_instance = new \Binance($environment->binance->accounts->{1}->apiKey, $environment->binance->accounts->{1}->apiSecret);
        if (!is_object($environment->binance_instance)) return [];

        $arr = $environment->binance_instance->ticker_24h();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        return $arr;
    }

    public static function get_balances()
    {
        global $environment;
        $environment->binance_instance = new \Binance($environment->binance->accounts->{1}->apiKey, $environment->binance->accounts->{1}->apiSecret);
        if (!is_object($environment->binance_instance)) return [];

        $arr = $environment->binance_instance->balances();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        return $arr;
    }

    public static function get_candlesticks($symbol, $interval = "5m", $limit = 1, $startTime = '', $endTime = '')
    {
        global $environment;
        $environment->binance_instance = new \Binance($environment->binance->accounts->{1}->apiKey, $environment->binance->accounts->{1}->apiSecret);
        if (!is_object($environment->binance_instance)) return [];

        $arr = $environment->binance_instance->candlesticks($symbol, $interval, $limit, $startTime, $endTime);
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