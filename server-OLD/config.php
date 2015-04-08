<?php

class config
{
    static private $config = null;
    
    static public function loadConfig($file = 'settings.json')
    {
        self::$config = json_decode(file_get_contents($file));
    }
    
    static public function getConfig()
    {
        return self::$config;
    }
}