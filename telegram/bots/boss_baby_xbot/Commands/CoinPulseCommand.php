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
class CoinPulseCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'coinpulse';

    /**
     * @var string
     */
    protected $description = 'Coin Pulse command';

    /**
     * @var string
     */
    protected $usage = '/coinpulse';

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
        // Get current config
        global $environment;

        $message   = $this->getMessage();
        // $chat_id   = $message->getChat()->getId();
        $chat_id   = $environment->telegram->channel->{1}->id;

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'html',
            'text' => PHP_EOL,
        ];

        // $data['text'] .= 'Message at ' . date('H:i:s d/m/Y');

        $list_coin_binance = \BossBaby\Telegram::get_coin_pulse_binance();
        // \BossBaby\Utility::writeLog('list_coin_binance:'.serialize($list_coin_binance));

        $list_coin_bittrex = \BossBaby\Telegram::get_coin_pulse_bittrex();
        // \BossBaby\Utility::writeLog('list_coin_bittrex:'.serialize($list_coin_bittrex));

        $list_coin = array_merge($list_coin_binance, $list_coin_bittrex);
        // \BossBaby\Utility::writeLog('list_coin:'.serialize($list_coin));
        
        if ($list_coin) {
            foreach ($list_coin as $coin) {
                $data['text'] = $coin . PHP_EOL;
                return Request::sendMessage($data);
            }
        }

        // Do nothing
        // return Request::emptyResponse();
    }
}
