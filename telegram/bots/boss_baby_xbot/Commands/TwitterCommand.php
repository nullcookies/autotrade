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
 * User "/twitter" command
 *
 * Command that processing everything about Twitter
 */
class TwitterCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'twitter';

    /**
     * @var string
     */
    protected $description = 'Processing for Twitter';

    /**
     * @var string
     */
    protected $usage = '/twitter';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));
        
        $message     = $this->getMessage();
        $chat_id     = $message->getChat()->getId();
        
        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'markdown',
        ];

        // $data = [
        //     'chat_id'      => $chat_id,
        //     'text'         => 'What do you want to do with Twitter?',
        //     'reply_markup' => Keyboard::forceReply(),
        // ];
        // return Request::sendMessage($data);

        // File store twitter data
        $twitter_config_file = CONFIG_DIR . '/twitter.php';
        $twitter_config = \BossBaby\Config::read($twitter_config_file);
        $twitter_config = \BossBaby\Utility::object_to_array($twitter_config);

        // List all keyword to filter
        $twitter_filter = (isset($twitter_config['filter']) and $twitter_config['filter']) ? (array) $twitter_config['filter'] : [];

        if ($twitter_filter) {
            $message = '*Các từ khoá đang được dùng để lọc*:' . PHP_EOL;
            foreach ($twitter_filter as $key => $value) {
                $message .= $value . PHP_EOL;
            }
            $message .= PHP_EOL;
            $data['text'] = $message;
            return Request::sendMessage($data);
        }

        $data['text'] = '__Hiện không có từ khóa nào được khai báo__';
        return Request::sendMessage($data);
    }
}
