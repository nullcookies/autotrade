<?php
namespace BossBaby;

class Bitmex
{
    public static function func_get_current_price($bitmex = null)
    {
        $arr = $bitmex->getTicker();
        if ($arr) {
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

    public static function func_get_open_orders($account_info = null)
    {
        if (!$account_info) return array();

        $arr = $account_info->getOpenOrders();
        return $arr;
    }

    public static function func_get_open_positions($account_info = null)
    {
        if (!$account_info) return array();

        $arr = $account_info->getOpenPositions();
        return $arr;
    }

    public static function func_get_margin($account_info = null)
    {
        if (!$account_info) return array();

        $arr = $account_info->getMargin();
        return $arr;
    }
}