<?php
namespace BossBaby;

class Config
{
    public static function read($filename)
    {
        if (!is_file($filename) or !file_exists($filename)) return [];

        $config = include $filename;
        return (array) $config;
    }
    
    public static function write($filename, array $config, $chmod = '')
    {
        if (!is_file($filename) or !file_exists($filename)) {
            $file = fopen($filename, 'w') or die('Error opening file: ' . $filename);
            fclose($file); 
        }

        $config = var_export((array) $config, true);
        $write = file_put_contents($filename, "<?php \nreturn $config;");
        sleep(1);

        if ($chmod) @chmod($filename, 0777);
        return $write;
    }

    public static function read_file($filename = '')
    {
        if (!is_file($filename) or !file_exists($filename)) return [];

        return trim(file_get_contents($filename));
    }
    
    public static function write_file($filename = '', $content = '', $chmod = '')
    {
        if (!is_file($filename) or !file_exists($filename)) {
            $file = fopen($filename, 'w') or die('Error opening file: ' . $filename);
            fclose($file); 
        }

        $write = file_put_contents($filename, trim($content));
        sleep(1);

        if ($chmod) @chmod($filename, 0777);
        return $write;
    }
}