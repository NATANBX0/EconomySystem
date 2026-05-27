<?php

declare(strict_types=1);

namespace EconomySystem;

use EconomySystem\Events\Account\PreTransferMoneyEvent;
use EconomySystem\Events\Account\TransferMoneyEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class ESListener implements Listener {

    public function onQuit(PlayerQuitEvent $e)
    {
        $player = $e->getPlayer();
        $playerName = $player->getName();
        $economyService = EconomySystem::getInstance()->getEconomyService();
        $economyService->saveAndClearCache($playerName);
    }

    // public function onPreTransfer(PreTransferMoneyEvent $e)
    // {
    //     $fromAccount = $e->getFromAccount();
    //     $toAccount = $e->getToAccount();
    //     $fromPlayer = EconomySystem::getInstance()->getServer()->getPlayer($fromAccount->getPlayerName());
    //     $fromPlayer->sendMessage('Voce esta enviando');
    //     if($e->getAmount() > 10)
    //     {
    //         $e->setCancelled(true);
    //         $fromPlayer->sendMessage('Cancelado quantidade enviada maior que 10');
    //     }
    // }

    // public function onTransfer(TransferMoneyEvent $e)
    // {
    //     $fromAccount = $e->getFromAccount();
    //     $fromPlayer = EconomySystem::getInstance()->getServer()->getPlayer($fromAccount->getPlayerName());
    //     $fromPlayer->sendMessage('Voce enviou');
    // }
}