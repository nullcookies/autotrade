<?php
namespace BossBaby;

class Config
{
    public static function read($filename)
    {
        $config = include $filename;
        return (array) $config;
    }
    
    public static function write($filename, array $config)
    {
        $config = var_export((array) $config, true);
        return file_put_contents($filename, "<?php \nreturn $config;");
    }

    public static function read_file($filename = '')
    {
        return trim(file_get_contents($filename));
    }
    
    public static function write_file($filename = '', $content = '')
    {
        return file_put_contents($filename, trim($content));
    }
}