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
    $list_symbol = ['XBTUSD', 'XBTU18', 'XBTZ18', 'ADAU18', 'BCHU18', 'EOSU18', 'ETHUSD', 'ETHU18', 'LTCU18', 'TRXU18', 'XRPU18'];

    $arr = [];
    $arr['symbols'] = [];
    $arr['last_updated'] = date('Y-m-d H:i:s');
    $arr['last_updated_unix'] = time();

    foreach ($list_symbol as $symbol) {
        $tmp = \BossBaby\Bitmex::func_get_current_price(null, $symbol);
        // \BossBaby\Utility::writeLog('tmp:'.serialize($tmp));
        if ($tmp) {
            $coin = $tmp['symbol'];
            $arr['symbols'][$coin] = $tmp;
            if (isset($tmp['lastPrice']))
                $arr['symbols'][$coin]['price'] = $tmp['lastPrice'];
        }
    }

    if ($arr) {
        // File store coin data
        $file = CONFIG_DIR . '/bitmex_coins.php';
        $file_tmp = $file . '.lock';
        // \BossBaby\Utility::writeLog('file:'.serialize($file));

        // Write overwrite to file
        \BossBaby\Config::write($file, $arr);
        unset($arr);
        sleep(1);

        if (is_file($file_tmp) and file_exists($file_tmp)) {
            @rename($file_tmp, $file);
            sleep(1);
        }

        // die('UPDATED');
    }

    // die('NOTHING');
}
