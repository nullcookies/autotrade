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
    
    public static function write($filename, array $config)
    {
        if (!is_file($filename) or !file_exists($filename)) {
            $file = fopen($filename, 'w') or die('Error opening file: ' . $filename);
            fclose($file); 
        }

        $config = var_export((array) $config, true);
        return file_put_contents($filename, "<?php \nreturn $config;");
    }

    public static function read_file($filename = '')
    {
        if (!is_file($filename) or !file_exists($filename)) return [];

        return trim(file_get_contents($filename));
    }
    
    public static function write_file($filename = '', $content = '')
    {
        if (!is_file($filename) or !file_exists($filename)) {
            $file = fopen($filename, 'w') or die('Error opening file: ' . $filename);
            fclose($file); 
        }

        return file_put_contents($filename, trim($content));
    }
}