<?php
namespace BossBaby;

class Bitmex
{
    public static function func_get_current_price($bitmex = null)
    {
        global $environment;
        $environment->bitmex_instance = new \Bitmex($environment->bittrex->accounts->{1}->apiKey, $environment->bittrex->accounts->{1}->apiSecret);
        
        $arr = $environment->bitmex_instance->getTicker();
        if (is_array($arr) and count($arr)) {
            $arr['marketPrice'] = $arr['market_price'];
            unset($arr['market_price']);
        }
        return $arr;
    }

    public static function func_get_account_info($account = null, $apiKey = null, $apiSecret = null, $hide_apiSecret = true)
    {
        $arr = array(
            'Account' => $account,
            'API Key' => $apiKey,
            'API Secret' => ($hide_apiSecret) ? \BossBaby\Utility::func_replace_by_star($apiSecret) : $apiSecret,
        );
        return $arr;
    }
    
    public static function func_get_account_wallet($account_info = null)
    {
        if (!$account_info) return array();

        $arr = $account_info->getWallet();
        return $arr;
    }

    public static function func_get_open_positions($account_info = null)
    {
        if (!$account_info) return array();

        $arr = $account_info->getOpenPositions();
        return $arr;
    }

    public static function func_get_open_orders($account_info = null)
    {
        if (!$account_info) return array();

        $arr = $account_info->getOpenOrders();
        return $arr;
    }

    public static function func_get_margin($account_info = null)
    {
        if (!$account_info) return array();

        $arr = $account_info->getMargin();
        return $arr;
    }
    
    public static function func_get_orderbook($account_info = null, $depth = 10)
    {
        if (!$account_info) return array();

        $arr = $account_info->getOrderBook($depth);
        return $arr;
    }

    public static function func_get_orders($account_info = null, $depth = 10)
    {
        if (!$account_info) return array();

        $arr = $account_info->getOrders($depth);
        return $arr;
    }

    public static function func_get_order($account_info = null, $orderID = 0, $count = 10)
    {
        if (!$account_info) return array();

        $arr = $account_info->getOrder($orderID, $count);
        return $arr;
    }
}