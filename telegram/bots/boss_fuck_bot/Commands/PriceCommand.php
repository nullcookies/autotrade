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
        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();
        $coin_name = trim($message->getText(true));

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];

        // Get current config
        global $environment;

        // If no command parameter is passed, show the list.
        if ($coin_name === '') {
            // $data['text'] = PHP_EOL;

            require_once(LIB_DIR . DS . "bitmex-api/BitMex.php");
            $environment->bitmex_instance = new \Bitmex($environment->bitmex->{1}->apiKey, $environment->bitmex->{1}->apiSecret);
            $arr = \BossBaby\Bitmex::func_get_current_price($environment->bitmex_instance);

            $_current_price = 0;
            $last_orig = $arr['last'];
            $last_sess = (isset($_current_price)) ? $_current_price : 0;
            $_current_price = $last_orig;
            // $arr['sess_last'] = $last_sess;
            
            if (!isset($_current_price)) {
                if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '👆 ' . $arr['last'];
                elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '👇 ' . $arr['last'];
            }
            else {
                if ($arr['last'] >= $last_sess) $arr['last'] = '👆 ' . $arr['last'];
                elseif ($arr['last'] < $last_sess) $arr['last'] = '👇 ' . $arr['last'];
            }
            if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '👆 ' . ($arr['lastChangePcnt'] * 100) . '%';
            elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '👇 ' . ($arr['lastChangePcnt'] * 100) . '%';
            else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

            $arr['Changed'] = $arr['lastChangePcnt']; unset($arr['lastChangePcnt']);

            $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $price = str_replace('Symbol:', '', $price);
            $data['text'] = $price;
            $data['text'] .= PHP_EOL;
            return Request::sendMessage($data);
        }

        $coin_name = str_replace('/', '', $coin_name);
        $data['text'] = 'Làm gì có *' . $coin_name . '*, thử lại coi 😒';
        
        require_once(LIB_DIR . DS . "binance-api/BinanceClass.php");
        $environment->binance_instance = new \Binance($environment->binance->{1}->apiKey, $environment->bitmex->{1}->apiSecret);
        $arr = \BossBaby\Binance::get_coin_price($environment->binance_instance, $coin_name);

        if ($arr) {
            // \BossBaby\Utility::writeLog('arr:'.serialize($arr).PHP_EOL.'-coin:'.serialize($coin_name));
            // $price = \BossBaby\Telegram::func_telegram_print_arr($arr);
            $data['text'] = 'Giá của *' . $coin_name . '* trên Binance:' . PHP_EOL . PHP_EOL;
            foreach ($arr as $key => $value) {
                $data['text'] .= str_replace($coin_name, $coin_name . '/', $key) . ': ' . $value . PHP_EOL;
            }
        }
        $data['text'] .= PHP_EOL;

        return Request::sendMessage($data);
    }
}
