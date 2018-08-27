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

/**
 * User "/twitter" command
 *
 * Command that processing everything about Twitter
 */
class ChartCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'chart';

    /**
     * @var string
     */
    protected $description = 'Processing chart';

    /**
     * @var string
     */
    protected $usage = '/chart or /chart <coin>';

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
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];
        
        $text = \BossBaby\Telegram::clean_command($message->getText(true));
        
        // chart coin
        if (stripos(str_replace('/chart ', '', $text), 'chart ') !== false) {
            $text = str_replace('chart ', '', str_replace('/chart ', '', $text));
        }

        // Get global environment
        global $environment;

        // If no command parameter is passed, show the list.
        if ($text === '' or $text === 'chart') {
            // $data['text'] = PHP_EOL;

            // Call file to draw chart
            \BossBaby\Shell::async_execute_file(__DIR__ . '/../draw-area-chart.php');

            // Get list file in chart folder
            $list_file = \BossBaby\Utility::list_file_in_directory(LOGS_DIR);
            // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::list_file::' . serialize($list_file));

            if (is_array($list_file) and count($list_file)) {
                $file_show = '';
                foreach ($list_file as $file) {
                    if (stripos($file, 'BTC-') !== false) {
                        $file_show = $file;
                        break;
                    }
                }

                $photo = $environment->general->root_url . '/logs/' . $file_show;
                // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::photo::' . serialize($photo));

                return Request::sendPhoto([
                    'chat_id' => $chat_id,
                    'photo'   => $photo,
                    'parse_mode' => 'markdown',
                ]);
            }
        }

        $data['text'] = 'There is no coin name *' . $text . '*, please try again 😒';

        // // Format current ALT's price
        // $price = \BossBaby\Telegram::format_alt_price_for_telegram($text);
        // if ($price) {
        //     $data['text'] = $price;
        //     return Request::sendMessage($data);
        // }
        
        $data['text'] .= PHP_EOL;
        return Request::sendMessage($data);
    }
}
