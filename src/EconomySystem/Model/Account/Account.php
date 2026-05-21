<?php

declare(strict_types=1);

namespace EconomySystem\Model\Account;

use EconomySystem\Utils\DynamicObject;
use pocketmine\Player;

class Account extends DynamicObject implements AccountInterface
{
    /** @var string */
    protected $playerName;
    /** @var int */
    protected $balance;

    public function __construct(string $playerName, int $balance)
    {
        $this->playerName = $playerName;
        $this->balance = $balance;
    }

    public function getBalance() : int
    {
        return $this->balance;
    }

    public function getPlayerName() : string
    {
        return $this->playerName;
    }

    public function setBalance(int $amount)
    {
        $this->balance = $amount;
    }

    public function withdraw(int $amount) : bool
    {
        $this->balance -= $amount;
        return true;
    }

    public function deposit(int $amount) 
    {
        $this->balance += $amount;
    }

    public function has(int $amount) : bool
    {
        return $this->balance >= $amount;
    }

    protected function serializeExtraData(): array
    {
        return 
        [
            self::PLAYER_NAME => $this->playerName,
            self::BALANCE => $this->balance
        ];
    }

    /**
     * @param array $data
     * @return Account|DynamicObject
     */
    public static function unserialize(array $data): DynamicObject
    {
        return new self
        (
            $data[self::PLAYER_NAME],
            $data[self::BALANCE]
        );
    }
}