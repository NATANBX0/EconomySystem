<?php

declare(strict_types=1);

namespace EconomySystem\Events\Account;

use EconomySystem\EconomySystem;
use EconomySystem\Events\EconomySystemEvent;
use EconomySystem\Model\Account\AccountInterface;
use pocketmine\event\Cancellable;

class PlayerBalanceReduceEvent extends EconomySystemEvent implements Cancellable {

    /** @var mixed */
    public static $handlerList = null;
    public static $eventPool = [];

    /** @var AccountInterface */
    private $account;
    /** @var int */
    private $amount;

    public function __construct(string $origin, AccountInterface $account, int $amount)
    {
        $this->account = $account;
        $this->amount = $amount;
        return parent::__construct(EconomySystem::getInstance(), $origin);
    }

    public function getAccount()
    {
        $this->account;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}