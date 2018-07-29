<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        // Don't know why it go here but, process
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $command = $message->getCommand();
        $text    = trim($message->getText(true));
        $from    = $message->getFrom();
        $user_id = $message->getFrom()->getId();

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];

        // // Get current config
        // global $environment;
        
        // Process show price of coin
        $coin_name = str_replace('/', '', $text);

        // Format current ALT's price
        $price = \BossBaby\Telegram::format_alt_price_for_telegram($coin_name);
        if ($price) {
            $data['text'] = $price;
            return Request::sendMessage($data);
        }

        // Get user-name
        if ($from->getFirstName() or $from->getLastName())
            $caption = sprintf('%s %s', $from->getFirstName(), $from->getLastName());
        else 
            $caption = sprintf('%s', $from->getUsername());

        // Process Hello
        if (str_replace('/hello ', '', $text) == 'Hello') {
            $message = 'Chào mày, *' . $caption . '*!';
        }

        // Process menu
        elseif (str_replace('/hello ', '', $text) == 'menu') {
            $message = '*Danh sách các lệnh có thể dùng*:' . PHP_EOL;
            $message .= PHP_EOL;
            // $data['text'] .= '/start - cái này khỏi nói làm gì' . PHP_EOL;
            // $data['text'] .= '/menu - hiển thị danh sách lệnh có thể dùng' . PHP_EOL;
            $message .= '/price - xem giá coin, mặc định là BTC' . PHP_EOL;
            $message .= PHP_EOL;
            $message .= 'tạm thời vậy thôi!!!' . PHP_EOL;
        }

        // Nothing to do
        else {
            $message = 'Mày muốn gì *' . $caption . '*';
        }

        $data['text'] = $message;
        return Request::sendMessage($data);

        // // Do nothing
        // return Request::emptyResponse();
    }

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
        
        $command = $message->getCommand();
        $text = trim($message->getText(true));

        if ($text === 'menu') {
            // 
        }

        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        //Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }

        return Request::emptyResponse();
    }
}
