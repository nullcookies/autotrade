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
 * Command that processing everything Alert
 */
class AlertCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'alert';

    /**
     * @var string
     */
    protected $description = 'Processing for Alert';

    /**
     * @var string
     */
    protected $usage = '/alert or /alert <coin> <price>';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $need_mysql = false;

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

        $text = trim($message->getText(true));

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

        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::text::' . serialize($text));
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::str_replace::' . serialize(str_replace('/twitter ', '', $text)));

        // Process add filter
        // twitter add filter test
        if (stripos($text, 'add filter') !== false) {
            $new_keyword = str_replace('add filter', '', $text);
            $new_keyword = trim($new_keyword);

            // Check if exists
            $is_exists = 0;
            foreach ($twitter_filter as $key => $keyword) {
                if ($keyword == $new_keyword) {
                    $is_exists = $key;
                    break;
                }
            }
            if ($is_exists) {
                $data['text'] = 'The keyword *' . $new_keyword . '* is exists!';
                return Request::sendMessage($data);
            }

            // Add to the list keyword
            $twitter_filter[] = $new_keyword;
            $twitter_config['filter'] = $twitter_filter;
            \BossBaby\Config::write($twitter_config_file, $twitter_config);

            $data['text'] = 'The keyword *' . $new_keyword . '* has been added into the list!';
            return Request::sendMessage($data);
        }

        // Process remove filter
        // twitter remove filter test
        if (stripos($text, 'del filter') !== false) {
            $new_keyword = str_replace('del filter', '', $text);
            $new_keyword = trim($new_keyword);

            // Check if not exists
            $is_exists = 0;
            foreach ($twitter_filter as $key => $keyword) {
                if ($keyword == $new_keyword) {
                    $is_exists = $key;
                    break;
                }
            }
            if (!$is_exists) {
                $data['text'] = 'The keyword *' . $new_keyword . '* is not exists!';
                return Request::sendMessage($data);
            }

            // Remove from list of keyword
            if (isset($twitter_filter[$is_exists]))
                unset($twitter_filter[$is_exists]);
            $twitter_config['filter'] = $twitter_filter;
            \BossBaby\Config::write($twitter_config_file, $twitter_config);

            $data['text'] = 'The keyword *' . $new_keyword . '* has been removed from the list!';
            return Request::sendMessage($data);
        }

        if ($twitter_filter) {
            $message = '*List keyword are using to filter*:' . PHP_EOL;
            foreach ($twitter_filter as $keyword) {
                $message .= $keyword . PHP_EOL;
            }
            $message .= PHP_EOL;
            $data['text'] = $message;
            return Request::sendMessage($data);
        }

        $data['text'] = '*You can type these commands*:' . PHP_EOL;
        $data['text'] .= PHP_EOL;
        $data['text'] .= '/alert <coin> <price> - set alert for a coin when it reach the price' . PHP_EOL;
        $data['text'] .= '/alert <coin> <expresion> <price> - e.g: /alert LTC >= 0.01' . PHP_EOL;
        $data['text'] .= '/alert del <coin> - remove alert for a coin' . PHP_EOL;
        $data['text'] .= '/alert show - show all your alert were set' . PHP_EOL;
        $data['text'] .= PHP_EOL;
        $data['text'] .= 'tạm thời vậy thôi!!!' . PHP_EOL;
        return Request::sendMessage($data);
    }
}
