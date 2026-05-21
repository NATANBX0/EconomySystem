<?php

declare(strict_types=1);

namespace EconomySystem\Model\Account;

interface AccountInterface {
    const PLAYER_NAME = 'player_name';
    const BALANCE = 'balance';

    /**
     * @return int
     */
    public function getBalance() : int;

    /**
     * @return string
     */
    public function getPlayerName() : string;

    /**
     * @param int $amount
     * @return bool
     */
    public function withdraw(int $amount) : bool;

    /**
     * @param int $amount
     * @return void
     */
    public function deposit(int $amount);

    /**
     * @param int $amount
     * @return bool
     */
    public function has(int $amount) : bool;

    /**
     * @param int $amount
     * @return void
     */
    public function setBalance(int $amount);
}