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
        $text = \BossBaby\Telegram::clean_command($message->getText(true));

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
            $message = 'Hi *' . $caption . '*!';
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
            $messages[] = 'Every trader has strengths and weakness. Some are good holders of winners, but may hold their losers a little too long. Others may cut their winners a little short, but are quick to take their losses. As long as you stick to your own style, you get the good and bad in your own approach. [Michael Marcus]';
            $messages[] = 'You can be free. You can live and work anywhere in the world. You can be independent from routine and not answer to anybody. [Alexander Elder]';
            $messages[] = 'A lot of people get so enmeshed in the markets that they lose their perspective. Working longer does not necessarily equate with working smarter. In fact, sometimes is the other way around. [Martin Schwartz]';
            $messages[] = 'I believe in analysis and not forecasting. [Nicolas Darvas]';
            $messages[] = 'A peak performance trader is totally committed to being the best and doing whatever it takes to be the best. He feels totally responsible for whatever happens and thus can learn from mistakes. These people typically have a working business plan for trading because they treat trading as a business.. [Van K. Tharp]';
            $messages[] = 'Win or lose, everybody gets what they want out of the market. Some people seem to like to lose, so they win by losing money. [Ed Seykota]';
            $messages[] = 'The secret to being successful from a trading perspective is to have an indefatigable and an undying and unquenchable thirst for information and knowledge. [Paul Tudor Jones]';
            $messages[] = 'What seems too high and risky to the majority generally goes higher and what seems low and cheap generally goes lower. [William O\'Neil]';
            $messages[] = 'Markets can remain irrational longer than you can remain solvent. [John Maynard Keynes]';
            $messages[] = 'You don\'t need to be a rocket scientist. Investing is not a game where the guy with the 160 IQ beats the guy with 130 IQ. [Warren Buffet]';
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
