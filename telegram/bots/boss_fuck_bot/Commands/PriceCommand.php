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

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * Price command
 *
 * Gets executed when a user check price
 */
class PriceCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'price';

    /**
     * @var string
     */
    protected $description = 'Price command';

    /**
     * @var string
     */
    protected $usage = '/price';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();
        $coin_name = trim($message->getText(true));

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];

        // If no command parameter is passed, show the list.
        if ($coin_name === '') {
            $data['text'] = PHP_EOL;

            // Get current config
            $config_file = __DIR__ . "/../config.php";
            $environment = parse_ini_file($config_file);
            
            $arr = [
                'file'    => $config_file,
                'environment' => $environment,
            ];

            $data['text'] .= json_encode($arr);
            return Request::sendMessage($data);

            
            if (!$environment->can_run) {
                $data['text'] .= PHP_EOL . 'STOP!!!';
                return Request::sendMessage($data);
            }

            // echo date('Y-m-d H:i:s') . ' -> ' . $environment->can_run . "\n";
            // if ($_check_price > 1) 
            // echo "\n";
            // echo 'Time: ' . date('Y-m-d H:i:s') . ' -> ' . $_check_price . "\n";

            $arr = func_get_current_price();

            $last_orig = $arr['last'];
            $last_sess = (isset($_current_price)) ? $_current_price : 0;
            $_current_price = $last_orig;
            // $arr['sess_last'] = $last_sess;
            
            if (!isset($_current_price)) {
                if ($arr['lastChangePcnt'] >= 0) $arr['last'] = '⬆ ' . $arr['last'];
                elseif ($arr['lastChangePcnt'] < 0) $arr['last'] = '⏬ ' . $arr['last'];
            }
            else {
                if ($arr['last'] >= $last_sess) $arr['last'] = '⬆ ' . $arr['last'];
                elseif ($arr['last'] < $last_sess) $arr['last'] = '⏬ ' . $arr['last'];
            }
            if ($arr['lastChangePcnt'] > 0) $arr['lastChangePcnt'] = '⬆ ' . ($arr['lastChangePcnt'] * 100) . '%';
            elseif ($arr['lastChangePcnt'] < 0) $arr['lastChangePcnt'] = '⏬ ' . ($arr['lastChangePcnt'] * 100) . '%';
            else $arr['lastChangePcnt'] =  ($arr['lastChangePcnt'] * 100) . '%';

            foreach ($arr as $key => $value) {
                if (is_object($value)) {
                    $data['text'] .= $key . ': ' . serialize($value) . PHP_EOL;
                }
                else {
                    $data['text'] .= $key . ': ' . $value . PHP_EOL;
                }
            }

            $data['text'] .= PHP_EOL;

            return Request::sendMessage($data);
        }

        $coin_name = str_replace('/', '', $coin_name);
        
        $data['text'] = 'Coin ' . $coin_name . ' not available for now';

        return Request::sendMessage($data);
    }
}
