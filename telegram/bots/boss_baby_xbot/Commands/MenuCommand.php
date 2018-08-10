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

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\InlineKeyboard;

/**
 * User "/menu" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class MenuCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'menu';

    /**
     * @var string
     */
    protected $description = 'Show bot commands help';

    /**
     * @var string
     */
    protected $usage = '/menu';

    /**
     * @var string
     */
    protected $version = '1.3.0';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));
        
        $message     = $this->getMessage();
        $chat_id     = $message->getChat()->getId();
        
        // Admin commands shouldn't be shown in group chats
        $safe_to_show = $message->getChat()->isPrivateChat();

        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'markdown',
        ];


        // // Hidekeyboard
        // $data = [
        //     'chat_id'      => $chat_id,
        //     'text'         => 'Keyboard Hidden',
        //     'reply_markup' => Keyboard::remove(),
        // ];
        // return Request::sendMessage($data);

        // // forcereply
        // $data = [
        //     'chat_id'      => $chat_id,
        //     'text'         => 'Write something:',
        //     'reply_markup' => Keyboard::forceReply(),
        // ];
        // return Request::sendMessage($data);

        // // inlinekeyboard
        // $switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';
        // $inline_keyboard = new InlineKeyboard([
        //     ['text' => 'inline', 'switch_inline_query' => $switch_element],
        //     ['text' => 'inline current chat', 'switch_inline_query_current_chat' => $switch_element],
        // ], [
        //     ['text' => 'callback', 'callback_data' => 'identifier'],
        //     ['text' => 'open url', 'url' => 'https://github.com/php-telegram-bot/core'],
        // ]);
        // $data = [
        //     'chat_id'      => $chat_id,
        //     'text'         => 'inline keyboard',
        //     'reply_markup' => $inline_keyboard,
        // ];
        // return Request::sendMessage($data);

        // //Keyboard examples
        // /** @var Keyboard[] $keyboards */
        // $keyboards = [];

        // //Example 0
        // $keyboards[] = new Keyboard(
        //     ['7', '8', '9'],
        //     ['4', '5', '6'],
        //     ['1', '2', '3'],
        //     [' ', '0', ' ']
        // );

        // //Example 1
        // $keyboards[] = new Keyboard(
        //     ['7', '8', '9', '+'],
        //     ['4', '5', '6', '-'],
        //     ['1', '2', '3', '*'],
        //     [' ', '0', ' ', '/']
        // );

        // //Example 2
        // $keyboards[] = new Keyboard('A', 'B', 'C');

        // //Example 3
        // $keyboards[] = new Keyboard(
        //     ['text' => 'A'],
        //     'B',
        //     ['C', 'D']
        // );

        // //Example 4 (bots version 2.0)
        // $keyboards[] = new Keyboard([
        //     ['text' => 'Send my contact', 'request_contact' => true],
        //     ['text' => 'Send my location', 'request_location' => true],
        // ]);

        // //Return a random keyboard.
        // $keyboard = $keyboards[mt_rand(0, count($keyboards) - 1)]
        //     ->setResizeKeyboard(true)
        //     ->setOneTimeKeyboard(true)
        //     ->setSelective(false);

        // $chat_id = $this->getMessage()->getChat()->getId();
        // $data    = [
        //     'chat_id'      => $chat_id,
        //     'text'         => 'Press a Button:',
        //     'reply_markup' => $keyboard,
        // ];

        // return Request::sendMessage($data);
        

        // Send photo
        // $result = Request::sendPhoto([
        //     'chat_id' => $chat_id,
        //     'photo'   => 'https://i.ytimg.com/vi/wfvxTyFJOiU/maxresdefault.jpg',
        // ]);

        // // Works
        // $keyboard = array(
        //     "inline_keyboard" => array(array(array("text" => "My Button Text", "callback_data" => "myCallbackData")))
        // );
        // $data['reply_markup'] = $keyboard;
        
        $data['text'] = '*Danh sách các lệnh có thể dùng*:' . PHP_EOL;
        $data['text'] .= PHP_EOL;
        // $data['text'] .= '/start - cái này khỏi nói làm gì' . PHP_EOL;
        // $data['text'] .= '/menu - hiển thị danh sách lệnh có thể dùng' . PHP_EOL;
        $data['text'] .= '/price - xem giá coin, mặc định là BTC' . PHP_EOL;
        $data['text'] .= PHP_EOL;
        $data['text'] .= 'tạm thời vậy thôi!!!' . PHP_EOL;

        return Request::sendMessage($data);
    }
}
