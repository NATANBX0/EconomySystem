<?php

declare(strict_types=1);

namespace EconomySystem\Model;

interface AccountInterface {
    const PLAYER_NAME = 'player_name';
    const BALANCE = 'balance';

    public function getBalance() : int;

    public function getPlayerName() : string;

    public function withdraw(int $amount) : bool;

    public function deposit(int $amount);

    public function has(int $amount) : bool;

    public function setBalance(int $amount);
}