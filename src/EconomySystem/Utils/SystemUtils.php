<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use pocketmine\event\Event;
use pocketmine\Player;
use pocketmine\Server;

class SystemUtils {

    /**
     * verify if a player still in the server
     * @param Player $player
     * @return bool
     */
    public static function isValidPlayer(Player $player) : bool
    {
        if($player instanceof Player || $player->isOnline())
        {
            return true;
        }
        return false;
    }

    public static function callEvent(Event $event)
    {
        Server::getInstance()->getPluginManager()->callEvent($event);
        return $event;
    }
}