<?php
namespace BossBaby;

class Bittrex
{
    public static function get_coin_price($bittrex = null, $coin = null)
    {
        if (!is_object($bittrex) or !$coin) return [];

        // $bittrex = new \Bittrex($environment->bittrex->{1}->apiKeynew \Bittrex($environment->bittrex->{1}->apiKey, $environment->bittrex->{1}->apiSecret);, $environment->bittrex->{1}->apiSecret, 'get');
        // $bittrex = 
        // $responce = $bittrex->GetCurrencies();

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
            $tmp = $bittrex->GetTicker($market);
            // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));
            if ($tmp->success) {
                $arr[$coin . '/' . $item] = number_format($tmp->result->Last, 8);
            }
        }

        return $arr;
    }
}