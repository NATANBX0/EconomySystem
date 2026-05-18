<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use EconomySystem\EconomySystem;

class Messages
{
    /**
     * @var Config
     */
    private static $messages;
    public static function init()
    {
        self::$messages = new Config(EconomySystem::getInstance()->getDataFolder() . 'messages.yml', Config::YAML);
    }

    /**
     * @return Config
     */
    public static function getConfig(): Config
    {
        return self::$messages;
    }

    /**
     * @param string $key
     */
    public static function has($key)
    {
        return self::getConfig()->exists($key);
    }

    /**
     * @param string $key
     * @param string|string[] $replace
     * @param string|string[] $to
     * @param mixed $defaultMessage
     */
    public static function get($key, $replace = null, $to = null, $defaultMessage = "Message not found"): string
    {
        if (!is_null($message = self::getConfig()->get($key, null))) {
            if (!is_null($replace)) {
                $message = str_replace($replace, $to, $message);
            }
        } else {
            EconomySystem::getInstance()->getLogger()->warning($defaultMessage);
        }
        return $message ?? $defaultMessage;
    }
    /**
     * @param Player|ConsoleCommandSender $player
     * @param string $key
     * @param string|string[] $replace
     * @param string|string[] $to
     * @param string $defaultMessage
     * @return void
     */
    public static function send($player, $key, $replace = null, $to = null, $defaultMessage = "Message not found")
    {
        $player->sendMessage(self::get($key, $replace, $to, $defaultMessage));
    }

    /**
     * 
     * @param string $key
     * @param string|string[] $value
     */
    public static function set($key, $value)
    {
        $config = self::getConfig();
        $config->set($key, $value);
        return $config->save();
    }
}