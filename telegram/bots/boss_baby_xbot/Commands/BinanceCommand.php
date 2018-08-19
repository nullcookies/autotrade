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
 * User "/binance" command
 *
 * Command that processing everything about Binance
 */
class BinanceCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'binance';

    /**
     * @var string
     */
    protected $description = 'Processing Binance';

    /**
     * @var string
     */
    protected $usage = '/binance or /binance <command>';

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
        if (stripos(str_replace('/binance ', '', $text), 'binance ') !== false) {
            $text = str_replace('binance ', '', str_replace('/binance ', '', $text));
        }

        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::chat_id::' . $chat_id . '::text::' . $text);

        // // Get global environment
        // global $environment;

        // If no command parameter is passed, show the list.
        if ($text === '' or $text === 'binance') {
            // $data['text'] = PHP_EOL;

            // Get balance on Binance
            $binance_balances = \BossBaby\Telegram::get_binance_balances();
            // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::binance_balances::' . serialize($binance_balances));

            $text = '';
            if (is_array($binance_balances) and count($binance_balances)) {
                // Get list current coin
                $file = CONFIG_DIR . '/binance_coins.php';
                $list_coin = \BossBaby\Config::read($file);
                if ($list_coin)
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
                
                $total = 0;
                $btc_price = (float) $list_coin['BTCUSDT'];
                
                foreach ($binance_balances as $coin => $item) {
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
                    elseif ($coin == 'BNB') {
                        // $item['btcValue'] = 0;
                        $text .= '-N: ' . $num_coin . ' ';
                        $text .= '-P: ' . $coin_price . 'BTC ';
                        $text .= '-E: ' . number_format($item['btcValue'], 4) . 'BTC ';
                        $text .= number_format(($item['btcValue'] * $btc_price), 2) . '$';
                    }
                    else {
                        if ($num_coin > 50)
                            $text .= '-N: ' . number_format($num_coin, 0) . ' ';
                        else
                            $text .= '-N: ' . number_format($num_coin, 2) . ' ';
                        $text .= '-P: ' . $coin_price . 'BTC ';
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
