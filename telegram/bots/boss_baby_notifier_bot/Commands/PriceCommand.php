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

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * Price command
 *
 * Gets executed when a user check price
 */
class PriceCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'price';

    /**
     * @var string
     */
    protected $description = 'Price command';

    /**
     * @var string
     */
    protected $usage = '/price or /price <coin>';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];

        $text = \BossBaby\Telegram::clean_command($message->getText(true));

        // Get global environment
        global $environment;

        $price = '';

        // price coin
        if (stripos(str_replace('/price ', '', $text), 'price ') !== false) {
            $text = str_replace('price ', '', str_replace('/price ', '', $text));
        }

        // If no command parameter is passed, show the list.
        if ($text === '' or $text === 'price') {
            // Format current XBT's price
            $price = \BossBaby\Telegram::format_xbt_price_for_telegram();
            $price = trim($price);

            if ($price) {
                // $data['text'] = 'Testing at ' . date('YmdHis');
                $data['text'] = $price . PHP_EOL;
                return Request::sendMessage($data);

                // Call file to draw chart
                \BossBaby\Shell::async_execute_file(__DIR__ . '/../../boss_baby_xbot/draw-area-chart.php');
                sleep(1);

                // Get list file in chart folder
                $list_file = \BossBaby\Utility::list_file_in_directory(LOGS_DIR);
                // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::list_file::' . serialize($list_file));

                if (is_array($list_file) and count($list_file)) {
                    $file_show = '';
                    foreach ($list_file as $file) {
                        if (stripos($file, 'BTC-') !== false) {
                            $file_show = $file;
                            break;
                        }
                    }

                    $photo = $environment->general->root_url . '/logs/' . $file_show;
                    // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::photo::' . serialize($photo));

                    return Request::sendPhoto([
                        'chat_id' => $chat_id,
                        'photo'   => $photo,
                        'parse_mode' => 'markdown',
                    ]);
                }
            }
        }

        $data['text'] = 'There is no coin name *' . $text . '*, please try again 😒';

        // // Format current ALT's price
        // $price = \BossBaby\Telegram::format_alt_price_for_telegram($text);
        // $price = trim($price);

        $file = CONFIG_DIR . '/bitmex_coins.php';
        $list_coin = \BossBaby\Config::read_file($file);
        $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
        if (!json_last_error() and $list_coin) {
            $list_coin = $list_coin['symbols'];
            $coin_name = strtoupper($text);

            $list_symbol_2dec = ['XBTUSD', 'XBTU18', 'XBTZ18', 'XBT7D_D95', 'XBT7D_U105', 'ETHUSD'];
            $list_symbol_5dec = ['BCHU18', 'ETHU18', 'LTCU18'];
            $list_symbol_8dec = ['ADAU18', 'EOSU18', 'TRXU18', 'XRPU18'];

            if ($coin_name == 'XBT' or $coin_name == 'BTC') $coin_name = 'XBTUSD';
            if ($coin_name == 'TRX') $coin_name = 'TRXU18';
            if ($coin_name == 'ADA') $coin_name = 'ADAU18';
            if ($coin_name == 'BCH') $coin_name = 'BCHU18';
            if ($coin_name == 'EOS') $coin_name = 'EOSU18';
            if ($coin_name == 'ETH') $coin_name = 'ETHU18';
            if ($coin_name == 'XRP') $coin_name = 'XRPU18';
            
            if (isset($list_coin[$coin_name])) {
                // $price .= '*BTC/USDT*' . PHP_EOL;
                $price .= '*' . $coin_name . '* on Bitmex:' . PHP_EOL;
                if (in_array($coin_name, $list_symbol_2dec)) {
                    $price .= 'Price: *' . number_format($list_coin[$coin_name]['price'], 2) . '*' . PHP_EOL;
                } elseif (in_array($coin_name, $list_symbol_5dec)) {
                    $price .= 'Price: *' . number_format($list_coin[$coin_name]['price'], 5) . '*' . PHP_EOL;
                } else {
                    $price .= 'Price: *' . number_format($list_coin[$coin_name]['price'], 8) . '*' . PHP_EOL;
                }
                $price .= 'Volume: ' . number_format($list_coin[$coin_name]['volume'], 2) . '' . PHP_EOL;
            }
            else {
                $price = 'Could not retrieve price of *' . $coin_name . '*';
            }
        }

        if ($price) {
            $data['text'] = $price;
            return Request::sendMessage($data);
        }
        
        $data['text'] .= PHP_EOL;
        return Request::sendMessage($data);
    }
}
