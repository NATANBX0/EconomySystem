<?php

declare(strict_types=1);

namespace EconomySystem\Events\Money;

use EconomySystem\EconomySystem;
use EconomySystem\Events\EconomySystemEvent;
use EconomySystem\Model\AccountInterface;
use pocketmine\event\Cancellable;

class TransferMoneyEvent extends EconomySystemEvent 
{
    public static $handlerList = null;

    /** @var AccountInterface */
    protected $fromAccount;
    /** @var AccountInterface */
    protected $toAccount;
    /** @var int */
    protected $amount;

    public function __construct(string $origin, AccountInterface $fromAccount, AccountInterface $toAccount, int $amount)
    {
        $this->fromAccount = $fromAccount;
        $this->toAccount = $toAccount;
        $this->amount = $amount;
        return parent::__construct(EconomySystem::getInstance(), $origin);
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