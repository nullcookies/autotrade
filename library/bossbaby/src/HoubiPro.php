<?php
namespace BossBaby;

class HoubiPro
{
    public static function get_coin_price($coin = null)
    {
        global $environment;
        
        defined('ACCOUNT_ID') or define('ACCOUNT_ID', $environment->houbi->accounts->{1}->userId); // 你的账户ID 
        defined('ACCESS_KEY') or define('ACCESS_KEY', $environment->houbi->accounts->{1}->apiKey); // 你的ACCESS_KEY
        defined('SECRET_KEY') or define('SECRET_KEY', $environment->houbi->accounts->{1}->apiSecret); // 你的SECRET_KEY
        
        $environment->houbipro_instance = new \req();

        if (!is_object($environment->houbipro_instance) or !$coin) return [];

        $arr = [];
        $coin = strtolower($coin);

        $coin_check = $coin . 'btc';
        $tmp = $environment->houbipro_instance->get_market_trade($coin_check);
        // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));
        if (is_object($tmp) and $tmp->status == 'ok' and $tmp->tick) {
            $tmp = $tmp->tick;
            $tmp = \BossBaby\Utility::object_to_array($tmp);
            if ($tmp['data'][0]['price'])
                $arr[strtoupper($coin . '/BTC')] = (float) $tmp['data'][0]['price'];
        }

        $coin_check = $coin . 'eth';
        $tmp = $environment->houbipro_instance->get_market_trade($coin_check);
        // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));
        if (is_object($tmp) and $tmp->status == 'ok' and $tmp->tick) {
            $tmp = $tmp->tick;
            $tmp = \BossBaby\Utility::object_to_array($tmp);
            if ($tmp['data'][0]['price'])
                $arr[strtoupper($coin . '/ETH')] = (float) $tmp['data'][0]['price'];
        }

        $coin_check = $coin . 'usdt';
        $tmp = $environment->houbipro_instance->get_market_trade($coin_check);
        // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));
        if (is_object($tmp) and $tmp->status == 'ok' and $tmp->tick) {
            $tmp = $tmp->tick;
            $tmp = \BossBaby\Utility::object_to_array($tmp);
            if ($tmp['data'][0]['price'])
                $arr[strtoupper($coin . '/USDT')] = (float) $tmp['data'][0]['price'];
        }

        // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin));
        return $arr;
    }

    public static function get_list_coin()
    {
        global $environment;
        
        defined('ACCOUNT_ID') or define('ACCOUNT_ID', $environment->houbi->accounts->{1}->userId); // 你的账户ID 
        defined('ACCESS_KEY') or define('ACCESS_KEY', $environment->houbi->accounts->{1}->apiKey); // 你的ACCESS_KEY
        defined('SECRET_KEY') or define('SECRET_KEY', $environment->houbi->accounts->{1}->apiSecret); // 你的SECRET_KEY
        
        $environment->houbipro_instance = new \req();

        if (!is_object($environment->houbipro_instance)) return [];

        $arr = [];
        $tmp = $environment->houbipro_instance->get_market_tickers();

        // $tmp = $environment->houbipro_instance->get_common_currencys();
        // sleep(1);
        // if (is_object($tmp) and $tmp->status == 'ok' and $tmp->data) {
        //     $tmp = $tmp->data;
        //     $tmp = \BossBaby\Utility::object_to_array($tmp);
        //     if ($tmp) {
        //         foreach ($tmp as $coin) {
        //             $coin = strtolower($coin);

        //             $coin_check = $coin . 'btc';
        //             $tmp = $environment->houbipro_instance->get_market_trade($coin_check);
        //             // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));
        //             if (is_object($tmp) and $tmp->status == 'ok' and $tmp->tick) {
        //                 $tmp = $tmp->tick;
        //                 $tmp = \BossBaby\Utility::object_to_array($tmp);
        //                 if ($tmp['data'][0]['price'])
        //                     $arr[strtoupper($coin . '/BTC')] = (float) $tmp['data'][0]['price'];
        //             }

        //             $coin_check = $coin . 'eth';
        //             $tmp = $environment->houbipro_instance->get_market_trade($coin_check);
        //             // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));
        //             if (is_object($tmp) and $tmp->status == 'ok' and $tmp->tick) {
        //                 $tmp = $tmp->tick;
        //                 $tmp = \BossBaby\Utility::object_to_array($tmp);
        //                 if ($tmp['data'][0]['price'])
        //                     $arr[strtoupper($coin . '/ETH')] = (float) $tmp['data'][0]['price'];
        //             }

        //             $coin_check = $coin . 'usdt';
        //             $tmp = $environment->houbipro_instance->get_market_trade($coin_check);
        //             // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-tmp:'.serialize($tmp));
        //             if (is_object($tmp) and $tmp->status == 'ok' and $tmp->tick) {
        //                 $tmp = $tmp->tick;
        //                 $tmp = \BossBaby\Utility::object_to_array($tmp);
        //                 if ($tmp['data'][0]['price'])
        //                     $arr[strtoupper($coin . '/USDT')] = (float) $tmp['data'][0]['price'];
        //             }
        //         }
        //     }
        // }

        if (is_object($tmp) and $tmp->status == 'ok' and $tmp->data) {
            $tmp = $tmp->data;
            $tmp = \BossBaby\Utility::object_to_array($tmp);
            foreach ($tmp as $coin) {
                // ["open"]=>
                // float(0.33055)
                // ["close"]=>
                // float(0.339687)
                // ["low"]=>
                // float(0.309005)
                // ["high"]=>
                // float(0.346)
                // ["amount"]=>
                // float(12655221.449643)
                // ["count"]=>
                // int(10779)
                // ["vol"]=>
                // float(4171700.6596849)
                // ["symbol"]=>
                // string(7) "paiusdt"
                $coin_name = $coin['symbol'];
            }
        }

        return $arr;
    }

    public static function get_balances()
    {
        // 
    }

    public static function get_candlesticks($symbol, $interval = "5m", $limit = 1, $startTime = '', $endTime = '')
    {
        // 
    }
}