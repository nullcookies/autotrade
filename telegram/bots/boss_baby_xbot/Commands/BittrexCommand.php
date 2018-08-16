<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/bittrex" command
 *
 * Command that processing everything about Binance
 */
class BittrexCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'bittrex';

    /**
     * @var string
     */
    protected $description = 'Processing Binance';

    /**
     * @var string
     */
    protected $usage = '/bittrex or /bittrex <command>';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $need_mysql = false;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];
        
        $text = trim($message->getText(true));
        $text = str_replace('/', '', $text);

        // chart coin
        if (stripos(str_replace('/bittrex ', '', $text), 'bittrex ') !== false) {
            $text = str_replace('bittrex ', '', str_replace('/bittrex ', '', $text));
        }

        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::chat_id::' . $chat_id . '::text::' . $text);

        // // Get global environment
        // global $environment;

        // If no command parameter is passed, show the list.
        if ($text === '' or $text === 'bittrex') {
            // $data['text'] = PHP_EOL;

            // Get balance on Bittrex
            $bittrex_balances = \BossBaby\Telegram::get_bittrex_balances();
            // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::bittrex_balances::' . serialize($bittrex_balances));

            $text = '';
            if (is_array($bittrex_balances) and count($bittrex_balances)) {
                // Try to get current price of all coin
                $file = CONFIG_DIR . '/bittrex_coins.php';
                $old_data = \BossBaby\Config::read_file($file);
                $old_data = \BossBaby\Utility::object_to_array(json_decode($old_data));
                if (!json_last_error() and $old_data and isset($old_data['10s']) and $old_data['10s'])
                    $list_coin = $old_data['10s'];
                else
                    $list_coin = \BossBaby\Bittrex::get_list_coin();
                $btc_price = $list_coin['USDT-BTC'];
                $btc_price = (float) str_replace(',', '', $btc_price);

                $total = 0;
                foreach ($bittrex_balances as $coin => $item) {
                    $text .= '*' . $coin . '* ';

                    // Calculate price and amount of BTC
                    $coin_price = 0;
                    if (array_key_exists('BTC-' . $coin, $list_coin)) {
                        $coin_price = $list_coin['BTC-' . $coin];
                        $coin_price = (float) str_replace(',', '', $coin_price);
                    }

                    $num_coin = $item['available'] + $item['onOrder'];
                    if ($list_coin and array_key_exists('BTC-' . $coin, $list_coin) and !in_array($coin, ['BTC','USDT'])) {
                        $num_coin = $item['available'] + $item['onOrder'];
                        $item['btcValue'] += ($num_coin * $coin_price);
                    }

                    if ($coin == 'BTC') {
                        $item['btcValue'] = $num_coin;
                        $text .= '-N: ' . $item['btcValue'] . ' ';
                        $text .= '-P: ' . number_format($btc_price, 2) . 'USDT ';
                        $text .= '-E: ' . number_format(($item['btcValue'] * $btc_price), 2) . '$';
                    }
                    elseif ($coin == 'USDT') {
                        $item['btcValue'] = 0;
                        $text .= '-N: ' . $num_coin . ' ';
                        $text .= $num_coin . '$';
                    }
                    else {
                        $text .= '-N: ' . number_format($num_coin, 2) . ' ';
                        $text .= '-P: ' . $coin_price . 'BTC ';
                        $text .= '-E: ' . number_format($item['btcValue'], 2) . 'BTC ';
                        $text .= number_format(($item['btcValue'] * $btc_price), 2) . '$';
                    }

                    $text .= PHP_EOL;
                    $total += $item['btcValue'];
                }
                $text .= PHP_EOL . '*Total*: ~' . number_format($total, 2) . 'BTC ' . number_format(($total * $btc_price), 2) . '$';

                // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::text::' . serialize($text));

                $data['text'] = $text;
                return Request::sendMessage($data);
            }
        }

        $data['text'] = 'Please try again 😒';

        // // Format current ALT's price
        // $price = \BossBaby\Telegram::format_alt_price_for_telegram($text);
        // if ($price) {
        //     $data['text'] = $price;
        //     return Request::sendMessage($data);
        // }
        
        $data['text'] .= PHP_EOL;
        return Request::sendMessage($data);
    }
}
