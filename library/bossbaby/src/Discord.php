<?php
namespace BossBaby;

class Discord
{
    public static function sendMessage($webhook_url = '', $message = '')
    {
        if (!$message) return null;
            // $message = 'Message send at ' . date('H:i:s d/m/Y');
        
        if ($webhook_url !== '' && $message !== '') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $webhook_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('content' => $message));
            // in real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            //          http_build_query(array('postvar1' => 'value1')));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            // if ($result->isOk()) {
            //     echo 'Message sent succesfully to: ' . $chat_id . PHP_EOL;
            // } else {
            //     echo 'Sorry message not sent to: ' . $chat_id . PHP_EOL;
            // }

            return $result;
        }
        else {
            die('Nothing to do!');
        }
    }
}