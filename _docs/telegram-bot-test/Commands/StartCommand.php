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
        //$message = $this->getMessage();
        //$chat_id = $message->getChat()->getId();
        //$user_id = $message->getFrom()->getId();

        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text    = 'Hi there!' . PHP_EOL . 'Type /help to see all commands!';

        $text    = PHP_EOL . "
remote: 
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCC1''''''''''''''''''''''''''''''''''''''''''tCCCCCCCCCC
remote: CCCCCCCCCC,                                          ;GCCCCCCCCC
remote: CCCCCCCCCCi                                          tCCCCCCCCCC
remote: CCCCCCCCCCf                                         .LCCCCCCCCCC
remote: CCCCCCCCCCC,                                        :CCCCCCCCCCC
remote: CCCCCCCCCCCi                                        tCCCCCCCCCCC
remote: CCCCCCCCCCCf                                       .LCCCCCCCCCCC
remote: CCCCCCCCCCCC.           .LCCCCCCCCCCCCfi11111111111tCCCCCCCCCCCC
remote: CCCCCCCCCCCC;            1CCCCCCCCCCCC1iiii11111111LCCCCCCCCCCCC
remote: CCCCCCCCCCCCt            :CCCCCCCCCCCCiiiiiii11111tCCCCCCCCCCCCC
remote: CCCCCCCCCCCCC.            LCCCCCCCCCCf;;;;iiiii111tCCCCCCCCCCCCC
remote: CCCCCCCCCCCCC;            iGCCCCCCCCCi;;;;;;iiiiiifCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCt            ,CCCCCCCCCC::::;;;;;;iiiLCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCC.            tLLLCCCCCt,:::::;;;;;;1CCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCC:              .....,,,,,,::::::;;;tCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCC1                ......,,,,,::::::;LCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCL                   .....,,,,,,:::;CCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCC:                    ......,,,,,:1CCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCC1                       .....,,,,fCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCL                         .....,:CCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCL:::::::::::::::::::::::::;;;;iLCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
remote: 
" . PHP_EOL;

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}
