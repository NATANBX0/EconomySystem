<?php

declare(strict_types=1);

namespace EconomySystem\Data\Account;

use EconomySystem\Model\Account\Account;
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
     * @param Account $account
     * @return void
     */
    public function save(Account $account);

    /**
     * @param string $playerName
     * @return bool
     */
    public function exists(string $playerName) : bool;
}