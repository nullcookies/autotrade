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
use Longman\TelegramBot\Request;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));
        
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $from    = $message->getFrom();
        // $user_id = $message->getFrom()->getId();

        if ($from->getFirstName() or $from->getLastName())
            $caption = sprintf('%s %s', $from->getFirstName(), $from->getLastName());
        else 
            $caption = sprintf('%s', $from->getUsername());

        // $text = 'Chào ' . $caption . '!' . PHP_EOL . 'Nếu không biết phải làm gì, hãy gõ /menu!';

        $text = '*List of command can be use*:' . PHP_EOL;
        $text .= PHP_EOL;
        // $text .= '/start - cái này khỏi nói làm gì' . PHP_EOL;
        // $text .= '/menu - hiển thị danh sách lệnh có thể dùng' . PHP_EOL;
        $text .= '/price - check price of coin, default BTC' . PHP_EOL;
        $text .= PHP_EOL;
        $text .= '... tobe continued' . PHP_EOL;

        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'markdown',
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}
