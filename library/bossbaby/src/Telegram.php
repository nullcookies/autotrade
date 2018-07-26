<?php
namespace BossBaby;

class Telegram
{
    public static function func_telegram_print_arr($arr = null) 
    {
        if (!$arr) return 'No data found!';
        $text = "\n";
        foreach ($arr as $key => $value) {
            if (is_object($value)) {
                $text .= ucfirst($key) . ': ' . serialize($value) . "\n";
            }
            else {
                $text .= ucfirst($key) . ': ' . $value . "\n";
            }
        }
        $text .= "\n";

        return $text;
    }
}