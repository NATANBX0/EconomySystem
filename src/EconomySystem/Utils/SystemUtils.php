<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use EconomySystem\Events\EconomySystemEvent;
use EconomySystem\Events\Account\PlayerBalanceIncreaseEvent;
use EconomySystem\Events\Account\PlayerBalanceReduceEvent;
use EconomySystem\Events\Account\PreTransferMoneyEvent;
use EconomySystem\Events\Account\TransferMoneyEvent;
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

    /**
     * @param Event $event
     * @return Event|EconomySystemEvent|PlayerBalanceIncreaseEvent|PlayerBalanceReduceEvent|PreTransferMoneyEvent|TransferMoneyEvent
     */
    public static function callEvent(Event $event)
    {
        Server::getInstance()->getPluginManager()->callEvent($event);
        return $event;
    }
}