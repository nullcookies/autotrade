<?php
namespace BossBaby;

class Bittrex
{
    public static function get_coin_price($coin_name = null)
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::coin:'.serialize($coin_name));
        
        if (!$coin_name) return [];

        // global $environment;
        // $environment->bittrex_instance = new \Bittrex($environment->bittrex->accounts->{1}->apiKey, $environment->bittrex->accounts->{1}->apiSecret);
        // if (!is_object($environment->bittrex_instance)) return [];

        // $environment->bittrex_instance = new \Bittrex($environment->bittrex->accounts->{1}->apiKeynew \Bittrex($environment->bittrex->accounts->{1}->apiKey, $environment->bittrex->accounts->{1}->apiSecret);, $environment->bittrex->accounts->{1}->apiSecret, 'get');
        // $responce = $environment->bittrex_instance->GetCurrencies();

        // require_once(LIB_DIR . DS . "bittrex-api-v7.1/src/BittrexManager.php");
        // // use codenixsv\Bittrex\BittrexManager;
        // $manager = new \BittrexManager($environment->bittrex->accounts->{1}->apiKey, $environment->bittrex->accounts->{1}->apiSecret);
        // $client = $manager->createClient();
        // $responce = $client->getBalances();
        // dump($client);

        // $markets = ['BTC', 'ETH', 'USDT', 'USD'];
        // // \BossBaby\Utility::writeLog('coin:'.serialize($coin_name).PHP_EOL.'-markets:'.serialize($markets));

        // $arr = [];
        // foreach ($markets as $item) {
        //     $market = $item . '-' . $coin_name;
        //     $tmp = $environment->bittrex_instance->GetTicker($market);
        //     // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));
        //     if ($tmp->success) {
        //         $arr[$coin_name . '/' . $item] = number_format($tmp->result->Last, 8);
        //     }
        // }

        // Try to get current price of all coin
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
        if ($list_coin) {
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
        $environment->bittrex_instance = new \Bittrex($environment->bittrex->accounts->{1}->apiKey, $environment->bittrex->accounts->{1}->apiSecret);
        if (!is_object($environment->bittrex_instance)) return [];

        $arr = $environment->bittrex_instance->GetMarketSummaries();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        if (!$arr or $arr->success != true or !$arr->result)
            return [];
        $arr = \BossBaby\Utility::object_to_array($arr->result);

        return $arr;
    }

    public static function get_balances()
    {
        global $environment;
        $environment->bittrex_instance = new \Bittrex($environment->bittrex->accounts->{1}->apiKey, $environment->bittrex->accounts->{1}->apiSecret);
        if (!is_object($environment->bittrex_instance)) return [];

        $arr = $environment->bittrex_instance->GetBalances();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        return $arr;
    }
}