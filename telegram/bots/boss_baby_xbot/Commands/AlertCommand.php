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
        $message_id  = $message->getMessageId();
        $chat        = $message->getChat();
        $chat_id     = $message->getChat()->getId();
        $from        = $message->getFrom();
        $user_id     = $message->getFrom()->getId();

        if ($from->getFirstName() or $from->getLastName())
            $caption = trim(sprintf('%s %s', $from->getFirstName(), $from->getLastName()));
        else 
            $caption = trim(sprintf('%s', $from->getUsername()));
        
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . PHP_EOL . '::message_id::' . serialize($message_id) . '::chat_id::' . serialize($chat_id) . '::user_id::' . serialize($user_id));
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . PHP_EOL . '::message::' . serialize($message) . '::chat_data::' . serialize($chat_data));
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . PHP_EOL . '::chat_id::' . serialize($chat_id) . '::from::' . serialize($from));
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . PHP_EOL . '::user_id::' . serialize($user_id));
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . PHP_EOL . '::getUsername::' . serialize($from->getUsername()) . '::getFirstName::' . serialize($from->getFirstName()) . '::getLastName::' . serialize($from->getLastName()));
        // exit;

        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'markdown',
        ];

        $text = \BossBaby\Telegram::clean_command($message->getText(true));

        // $data = [
        //     'chat_id'      => $chat_id,
        //     'text'         => 'What do you want to do with Twitter?',
        //     'reply_markup' => Keyboard::forceReply(),
        // ];
        // return Request::sendMessage($data);

        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::text::' . serialize($text));
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::str_replace::' . serialize(str_replace('/twitter ', '', $text)));

        // File store twitter data
        $alert_file = CONFIG_DIR . '/coin_alert.php';
        $alert_data = \BossBaby\Config::read($alert_file);
        $alert_data = \BossBaby\Utility::object_to_array($alert_data);

        if (!$alert_data) $alert_data = [];

        if ($user_id) {
            if (!array_key_exists($user_id, $alert_data))
                $alert_data[$user_id] = ['info'=>[], 'alert'=>[]];
            
            if (!isset($alert_data[$user_id]['info']['id']) or !$alert_data[$user_id]['info']['id'])
                $alert_data[$user_id]['info']['id'] = $user_id;

            $alert_data[$user_id]['info']['is_bot'] = $from->getIsBot();
            $alert_data[$user_id]['info']['username'] = $from->getUsername();
            $alert_data[$user_id]['info']['first_name'] = $from->getFirstName();
            $alert_data[$user_id]['info']['last_name'] = $from->getLastName();
            $alert_data[$user_id]['info']['language_code'] = $from->getLanguageCode();            

            // Write to file
            \BossBaby\Config::write($alert_file, $alert_data);

            // twitter add filter test
            if (stripos($text, 'set') !== false) {
                $text = str_ireplace('set', '', $text);
                $text = \BossBaby\Telegram::clean_command($text);
                $tmp = explode(' ', $text);
                if (is_array($tmp) and count($tmp) >= 2) {
                    if (count($tmp) == 3) {
                        // 
                    }
                    elseif (count($tmp) == 2) {
                        // 
                    }
                }
            }
            elseif (1==2) {
                // 
            }
            else {
                if (isset($alert_data[$user_id]['alert']) and is_array($alert_data[$user_id]['alert']) and count($alert_data[$user_id]['alert'])) {
                    $message = '*List coin you were set before*:' . PHP_EOL;
                    foreach ($alert_data[$user_id]['alert'] as $coin) {
                        $message .= $keyword . PHP_EOL;
                    }
                    $message .= PHP_EOL;
                }
                else {
                    $message = '*You can run these commands*:' . PHP_EOL;
                    $message .= '/alert set <coin-name> [condition] <price/BTC>' . PHP_EOL;
                    $message .= '/alert remove <number>' . PHP_EOL;
                    $message .= '/alert remove <coin-name>' . PHP_EOL;
                    $message .= '/alert' . PHP_EOL;
                }

                $data['text'] = $message;
                return Request::sendMessage($data);
            }
        }
        else {
            $data['text'] = 'Something went wrong! We can\'t find your user-data, please comeback later!';
            return Request::sendMessage($data);
        }
        
        exit;

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

        return Request::emptyResponse();
    }
}
