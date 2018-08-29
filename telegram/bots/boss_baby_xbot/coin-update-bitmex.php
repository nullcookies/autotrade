<?php
/**
 * README
 * This configuration file is intended to run a list of commands with crontab.
 * Uncommented parameters must be filled
 */

if (!defined('STDIN')) die('Access denied.' . "\n");

// Error handle
require_once __DIR__ . '/../error-handle.php';

// // Load composer
// require_once LIB_DIR . '/bitmex-api/api-connectors/auto-generated/php/SwaggerClient-php/vendor/autoload.php';

run_cron();
$sleep = 10;
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();
sleep($sleep); run_cron();

function run_cron() {
    // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::' . date('YmdHis'));

    // Run cron to update coin from exchange
    // $list_symbol = ['XBTUSD'];
    // $list_symbol = ['XBTUSD', 'ADAU18', 'BCHU18', 'ETHUSD', 'LTCU18', 'EOSU18', 'TRXU18', 'XRPU18'];
    $list_symbol = ['XBTUSD', 'XBTU18', 'XBTZ18', 'XBT7D_D95', 'XBT7D_U105', 'ADAU18', 'BCHU18', 'EOSU18', 'ETHUSD', 'ETHU18', 'LTCU18', 'TRXU18', 'XRPU18'];

    // File store coin data
    $file = CONFIG_DIR . '/bitmex_coins.php';
    $file_tmp = $file . '.lock';
    // \BossBaby\Utility::writeLog('file:'.serialize($file));

    $arr = [];
    if (is_file($file) and file_exists($file)) {
        $arr = \BossBaby\Config::read_file($file);
        $arr = \BossBaby\Utility::object_to_array(json_decode($arr));
    }

    if (json_last_error() or !$arr)
        $arr = [];
    $arr['symbols'] = [];
    $arr['last_updated'] = date('Y-m-d H:i:s');
    $arr['last_updated_unix'] = time();

    $tmp = \BossBaby\Bitmex::func_get_current_price(null, null);
    // dump($tmp);die;
    // \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::symbol::' . $symbol);
    // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));
    if ($tmp) {
        $tmp = \BossBaby\Utility::object_to_array($tmp);
        foreach ($tmp as $entry) {
            $coin = $entry['symbol'];
            if (!$coin or !in_array($coin, $list_symbol))
                continue;
            $arr['symbols'][$coin] = $entry;
            if (isset($entry['lastPrice']))
                $arr['symbols'][$coin]['price'] = $entry['lastPrice'];
        }
    }
    
    if ($arr) {
        // Write overwrite to file
        $arr = json_encode($arr);
        \BossBaby\Config::write_file($file, $arr);
        unset($arr);
        sleep(1);

        if (is_file($file_tmp) and file_exists($file_tmp)) {
            @rename($file_tmp, $file);
            // sleep(1);
        }

        // die('UPDATED');
    }

    // die('NOTHING');
}
