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
 * ConfigCommand command
 *
 * Set configs
 */
class ConfigCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'config';

    /**
     * @var string
     */
    protected $description = 'Config command';

    /**
     * @var string
     */
    protected $usage = '/config';

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
        // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

        $message   = $this->getMessage();
        $chat_id   = $message->getChat()->getId();
        $text      = trim($message->getText(true));

        \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::text::' . serialize($text));

        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'markdown',
        ];

        // File store config data
        $coin_config_file = CONFIG_DIR . '/coin_config.php';
        if (is_file($coin_config_file) and file_exists($coin_config_file)) {
            $coin_config = \BossBaby\Config::read($coin_config_file);
            $coin_config = \BossBaby\Utility::object_to_array($coin_config);
        }
        else {
            $coin_config = [];
            $coin_config['coin_volume']['binance']['max_volume'] = 2;
            $coin_config['coin_volume']['binance']['max_changed'] = 0.5;
            \BossBaby\Config::write($coin_config_file, $coin_config);
        }

        // Process set coinvol
        if (stripos($text, 'set coin_volume binance max_volume') !== false) {
            $value = str_replace('set coin_volume binance max_volume', '', $text);
            $value = trim($value);

            $coin_config['coin_volume']['binance']['max_volume'] = $value;
            \BossBaby\Config::write($coin_config_file, $coin_config);

            $data['text'] = 'Max volume has been set to *' . $value . '%*';
            return Request::sendMessage($data);
        }

        // Process set coinvol
        if (stripos($text, 'set coin_volume binance max_changed') !== false) {
            $value = str_replace('set coin_volume binance max_changed', '', $text);
            $value = trim($value);

            $coin_config['coin_volume']['binance']['max_changed'] = $value;
            \BossBaby\Config::write($coin_config_file, $coin_config);

            $data['text'] = 'Max changed has been set to *' . $value . '%*';
            return Request::sendMessage($data);
        }

        $data['text'] = '*Current config*:' . PHP_EOL;
        $data['text'] .= 'coin_volume - binance - max_volume: ' . $coin_config['coin_volume']['binance']['max_volume'] . '%' . PHP_EOL;
        $data['text'] .= 'coin_volume - binance - max_changed: ' . $coin_config['coin_volume']['binance']['max_changed'] . '%' . PHP_EOL;
        return Request::sendMessage($data);
    }
}
