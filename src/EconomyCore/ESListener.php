<?php

declare(strict_types=1);

namespace EconomySystem;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class ESListener implements Listener {

    public function onQuit(PlayerQuitEvent $e)
    {
        $player = $e->getPlayer();
        $playerName = $player->getName();
        $economyService = EconomySystem::getInstance()->getEconomyService();
        $economyService->saveAndClearCache($playerName);
    }
}