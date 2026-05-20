<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use pocketmine\utils\Config;
use EconomySystem\EconomySystem;

class EconomySystemConfig
{

    public static function getConfig(): Config
    {
        return EconomySystem::getInstance()->getConfig();
    }

    /**
     * @param string $key
     */
    public static function has(string $key)
    {
        return self::getConfig()->exists($key);
    }

    /**
     * @param string $key
     * @param mixed $default
     */
    public static function get($key, $default = false)
    {
        return self::getConfig()->get($key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, $value)
    {
        $config = self::getConfig();
        $config->set($key, $value);
        return $config->save();
    }
}