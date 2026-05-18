<?php

declare(strict_types=1);

namespace EconomySystem\Data;

use EconomySystem\Model\Account;
use EconomySystem\Data\Exception\AccountNotFoundException;
use EconomySystem\Model\AccountInterface;
use EconomySystem\Utils\Promise\Promise;

interface AccountDataInterface {
    const PLAYER_NAME = 'player_name';
    const BALANCE = 'balance';

    /**
     * @param string $playerName
     * @return Promise
     */
    public function load(string $playerName) : Promise;

    /**
     * @param AccountInterface $account
     * @return void
     */
    public function save(AccountInterface $account);

    /**
     * @param string $playerName
     * @return bool
     */
    public function exists(string $playerName) : bool;
}