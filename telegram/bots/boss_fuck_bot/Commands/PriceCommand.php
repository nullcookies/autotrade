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
    protected $usage = '/price';

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
        //$message = $this->getMessage();
        //$chat_id = $message->getChat()->getId();
        //$user_id = $message->getFrom()->getId();

        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text    = 'Hi there!' . PHP_EOL . 'Type /help to see all commands!';

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}
