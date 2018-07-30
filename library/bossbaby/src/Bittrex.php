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

        $coins = [];
        $coins[] = 'BTC-' . $coin;
        $coins[] = 'ETH-' . $coin;
        $coins[] = 'USD-' . $coin;
        // \BossBaby\Utility::writeLog('coins:'.serialize($coins).PHP_EOL.'-coin:'.serialize($coin));

        $arr = [];
        foreach ($coins as $coin) {
            $tmp = $bittrex->GetTicker($coin);
            // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));
            if ($tmp->success) {
                $arr[$coin] = $tmp->result->Last;
            }
        }

        return $arr;
    }
}