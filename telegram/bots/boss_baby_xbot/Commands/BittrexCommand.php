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
     * @var bool
     */
    protected $private_only = true;

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
                
                $total = 0;
                $btc_price = (float) $list_coin['BTCUSDT'];

                foreach ($bittrex_balances as $coin => $item) {
                    $text .= '*' . $coin . '* ';

                    // Calculate price and amount of BTC
                    $coin_price = 0;
                    if (array_key_exists($coin . 'BTC', $list_coin))
                        $coin_price = $list_coin[$coin . 'BTC'];

                    $num_coin = $item['available'] + $item['onOrder'];
                    if ($list_coin and array_key_exists($coin . 'BTC', $list_coin) and !in_array($coin, ['BTC','USDT'])) {
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
                        $text .= '-E: ' . $num_coin . '$';
                    }
                    else {
                        if ($num_coin > 50)
                            $text .= '-N: ' . number_format($num_coin, 0) . ' ';
                        else
                            $text .= '-N: ' . number_format($num_coin, 2) . ' ';
                        $text .= '-P: ' . number_format($coin_price, 8) . 'BTC ';
                        $text .= '-E: ' . number_format($item['btcValue'], 4) . 'BTC ';
                        $text .= number_format(($item['btcValue'] * $btc_price), 2) . '$';
                    }

                    $text .= PHP_EOL;
                    $total += $item['btcValue'];
                }
                $text .= PHP_EOL . '*Total*: ~*' . number_format($total, 8) . '*BTC (' . number_format(($total * $btc_price), 2) . '$)';

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
