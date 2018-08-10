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
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

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

        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::text::' . serialize($text));

        // Get current config
        global $environment;
        
        // Process show price of coin
        $coin_name = str_replace('/', '', $text);

        // Format current ALT's price
        $price = \BossBaby\Telegram::format_alt_price_for_telegram($coin_name);
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::price::' . serialize($price));
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
        if ($text == 'hello') {
            $message = 'Chào mày, *' . $caption . '*!';

            $data['text'] = $message;
            return Request::sendMessage($data);
        }

        // Process menu
        elseif ($text == 'menu') {
            $message = '*Danh sách các lệnh có thể dùng*:' . PHP_EOL;
            $message .= PHP_EOL;
            // $data['text'] .= '/start - cái này khỏi nói làm gì' . PHP_EOL;
            // $data['text'] .= '/menu - hiển thị danh sách lệnh có thể dùng' . PHP_EOL;
            $message .= '/price - xem giá coin, mặc định là BTC' . PHP_EOL;
            $message .= PHP_EOL;
            $message .= 'tạm thời vậy thôi!!!' . PHP_EOL;

            $data['text'] = $message;
            return Request::sendMessage($data);
        }

        // Process menu
        elseif ($text == 'twitter filter') {
            // File store twitter data
            $twitter_config_file = CONFIG_DIR . '/twitter.php';
            $twitter_config = \BossBaby\Config::read($twitter_config_file);
            $twitter_config = \BossBaby\Utility::object_to_array($twitter_config);

            $twitter_filter = (isset($twitter_config['filter']) and $twitter_config['filter']) ? (array) $twitter_config['filter'] : [];

            if ($twitter_filter) {
                $message = '*Các từ khoá đang được dùng để lọc*:' . PHP_EOL;
                $message .= PHP_EOL;
                foreach ($twitter_filter as $key => $value) {
                    $message .= $value . PHP_EOL;
                }
                $data['text'] = $message;
                return Request::sendMessage($data);
            }
        }

        // Nothing to do
        else {
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
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

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
