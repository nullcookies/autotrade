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
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];

        $text = trim($message->getText(true));
        $text = str_replace('/', '', $text);

        // Get global environment
        global $environment;

        $price = '';

        // price coin
        if (stripos(str_replace('/price ', '', $text), 'price ') !== false) {
            $text = str_replace('price ', '', str_replace('/price ', '', $text));
        }

        // If no command parameter is passed, show the list.
        if ($text === '' or $text === 'price') {
            // // Format current XBT's price
            // $price = \BossBaby\Telegram::format_xbt_price_for_telegram();
            // $price = trim($price);

            $file = CONFIG_DIR . '/binance_coins.php';
            $list_coin = \BossBaby\Config::read_file($file);
            $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
            if (!json_last_error() and $list_coin) {
                $list_coin = $list_coin['symbols'];
                $coin_name = strtoupper($text);
                if (isset($list_coin['BTCUSDT'])) {
                    $price .= '*BTC/USDT*' . PHP_EOL;
                    $price .= 'Binance: ' . number_format($list_coin['BTCUSDT']['price'], 2) . PHP_EOL;
                }
            }
            $file = CONFIG_DIR . '/bittrex_coins.php';
            $list_coin = \BossBaby\Config::read_file($file);
            $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
            if (!json_last_error() and $list_coin) {
                $list_coin = $list_coin['symbols'];
                $coin_name = strtoupper($text);
                if (isset($list_coin['BTCUSDT'])) {
                    // $price .= '*BTC/USDT*' . PHP_EOL;
                    $price .= 'Bittrex: ' . number_format($list_coin['BTCUSDT']['price'], 2) . PHP_EOL;
                }
            }
            $file = CONFIG_DIR . '/houbipro_coins.php';
            $list_coin = \BossBaby\Config::read_file($file);
            $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
            if (!json_last_error() and $list_coin) {
                $list_coin = $list_coin['symbols'];
                $coin_name = strtoupper($text);
                if (isset($list_coin['BTCUSDT'])) {
                    // $price .= '*BTC/USDT*' . PHP_EOL;
                    $price .= 'HoubiPro: ' . number_format($list_coin['BTCUSDT']['price'], 2) . PHP_EOL;
                }
            }
            $file = CONFIG_DIR . '/bitmex_coins.php';
            $list_coin = \BossBaby\Config::read_file($file);
            $list_coin = \BossBaby\Utility::object_to_array(json_decode($list_coin));
            if (!json_last_error() and $list_coin) {
                $list_coin = $list_coin['symbols'];
                $coin_name = strtoupper($text);
                if (isset($list_coin['XBTUSD'])) {
                    // $price .= '*BTC/USDT*' . PHP_EOL;
                    $price .= 'Bitmex: ' . number_format($list_coin['XBTUSD']['price'], 2) . PHP_EOL;
                }
            }

            if ($price) {
                // $data['text'] = 'Testing at ' . date('YmdHis');
                $data['text'] = $price . PHP_EOL;
                Request::sendMessage($data);

                // Call file to draw chart
                \BossBaby\Shell::async_execute_file(__DIR__ . '/../draw-area-chart.php');
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

        // Format current ALT's price
        $price = \BossBaby\Telegram::format_alt_price_for_telegram($text);
        $price = trim($price);

        if ($price) {
            $data['text'] = $price;
            return Request::sendMessage($data);
        }
        
        $data['text'] .= PHP_EOL;
        return Request::sendMessage($data);
    }
}
