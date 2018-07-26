<?php
namespace BossBaby;

class Config
{
    public static function read($filename)
    {
        $config = include $filename;
        return (object) $config;
    }
    
    public static function write($filename, array $config)
    {
        $config = var_export((array) $config, true);
        file_put_contents($filename, "<?php \nreturn $config;");
    }
}