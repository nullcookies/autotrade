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

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\InlineKeyboard;

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
    protected $need_mysql = false;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

        // Don't know why it go here but, process
        // Found the reason: need_mysql = true
        
        // Do nothing
        return Request::emptyResponse();
    }

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
        
        $command = $message->getCommand();
        $text = trim($message->getText(true));

        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::text::' . serialize($text));
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::str_replace::' . serialize(str_replace('/twitter ', '', $text)));

        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        //Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }

        // -------------------- Add more -------------------- //
        $from    = $message->getFrom();
        $user_id = $message->getFrom()->getId();

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];
        
        // Get current config
        global $environment;
        
        // Get user-name
        if ($from->getFirstName() or $from->getLastName())
            $caption = sprintf('%s %s', $from->getFirstName(), $from->getLastName());
        else 
            $caption = sprintf('%s', $from->getUsername());

        // Process Hello
        if (str_replace('/hello ', '', $text) == 'hello') {
            $message = 'Chào mày, *' . $caption . '*!';
            $data['text'] = $message;
            return Request::sendMessage($data);
        }

        // Process menu
        elseif (str_replace('/menu ', '', $text) == 'menu') {
            return $this->telegram->executeCommand($text);
        }

        // Process menu
        elseif (str_replace('/twitter ', '', $text) == 'twitter') {
            return $this->telegram->executeCommand($text);
        }

        // Process menu
        elseif (stripos('twitter add filter', $text) !== false or stripos('twitter del filter', $text) !== false) {
            $text = str_replace('twitter', '', $text);
            return $this->telegram->executeCommand('twitter');
        }

        // Process price
        elseif (str_replace('/price ', '', $text) == 'price' or stripos(str_replace('/price ', '', $text), 'price ') !== false) {
            return $this->telegram->executeCommand('price');
        }

        // Nothing to do
        else {
            // Process show price of coin
            $coin_name = str_replace('/', '', $text);
            // Format current ALT's price
            $price = \BossBaby\Telegram::format_alt_price_for_telegram($coin_name);
            // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::price::' . serialize($price));
            if ($price) {
                $data['text'] = $price;
                return Request::sendMessage($data);
            }

            $messages = [];
            $messages[] = 'Mày muốn gì *' . $caption . '*';
            $messages[] = 'Chúng mày muốn cái gì?';
            $messages[] = 'Hello';
            $messages[] = "How are you doing?";
            $messages[] = "Howdy!";
            $message = $messages[rand(0, count($messages)-1)];
            
            if (rand(1,1000) % 3 == 0) {
                $data['text'] = $message;
                return Request::sendMessage($data);
            }
        }

        // -------------------- Add more -------------------- //


        return Request::emptyResponse();
    }
}