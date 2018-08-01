<?php
namespace BossBaby;

class Bittrex
{
    public static function get_coin_price($coin = null)
    {
        global $environment;
        $environment->bittrex_instance = new \Bittrex($environment->bittrex->{1}->apiKey, $environment->bittrex->{1}->apiSecret);
        
        if (!is_object($environment->bittrex_instance) or !$coin) return [];

        // $environment->bittrex_instance = new \Bittrex($environment->bittrex->{1}->apiKeynew \Bittrex($environment->bittrex->{1}->apiKey, $environment->bittrex->{1}->apiSecret);, $environment->bittrex->{1}->apiSecret, 'get');
        // $responce = $environment->bittrex_instance->GetCurrencies();

        // require_once(LIB_DIR . DS . "bittrex-api-v7.1/src/BittrexManager.php");
        // // use codenixsv\Bittrex\BittrexManager;
        // $manager = new \BittrexManager($environment->bittrex->{1}->apiKey, $environment->bittrex->{1}->apiSecret);
        // $client = $manager->createClient();
        // $responce = $client->getBalances();
        // dump($client);

        $markets = ['BTC', 'ETH', 'USDT', 'USD'];
        // \BossBaby\Utility::writeLog('coin:'.serialize($coin).PHP_EOL.'-markets:'.serialize($markets));

        $arr = [];
        foreach ($markets as $item) {
            $market = $item . '-' . $coin;
            $tmp = $environment->bittrex_instance->GetTicker($market);
            // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));
            if ($tmp->success) {
                $arr[$coin . '/' . $item] = number_format($tmp->result->Last, 8);
            }
        }

        return $arr;
    }

    public static function get_list_coin()
    {
        global $environment;
        $environment->bittrex_instance = new \Bittrex($environment->bittrex->{1}->apiKey, $environment->bittrex->{1}->apiSecret);
        
        if (!is_object($environment->bittrex_instance)) return [];

        $arr = $environment->bittrex_instance->GetMarketSummaries();
        // \BossBaby\Utility::writeLog('arr:'.serialize($arr));

        return $arr;
    }
}