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
