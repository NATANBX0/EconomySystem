<?php

declare(strict_types=1);

namespace EconomySystem\Events\Account;

use EconomySystem\EconomySystem;
use EconomySystem\Events\EconomySystemEvent;
use EconomySystem\Model\Account\AccountInterface;
use pocketmine\event\Cancellable;

class PreTransferMoneyEvent extends EconomySystemEvent implements Cancellable {

    public static $handlerList = null;
    public static $eventPool = [];

    /** @var AccountInterface */
    private $fromAccount;
    /** @var AccountInterface */
    private $toAccount;
    /** @var int */
    private $amount;

    public function __construct(string $origin, AccountInterface $fromAccount, AccountInterface $toAccount, int $amount)
    {
        $this->fromAccount = $fromAccount;
        $this->toAccount = $toAccount;
        $this->amount = $amount;
        parent::__construct(EconomySystem::getInstance(), $origin);
    }

    public function getFromAccount() : AccountInterface 
    {
        return $this->fromAccount;
    }

    public function getToAccount() : AccountInterface 
    {
        return $this->toAccount;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}