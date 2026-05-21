<?php

declare(strict_types=1);

namespace EconomySystem\Data\Debt;

use EconomySystem\Model\AccountDebt\DebtInterface;
use EconomySystem\Model\Debt\Debt;
use EconomySystem\Utils\Promise\Promise;

interface DebtDataInterface 
{
    /**
     * @param string $player
     * @return Promise
     */
    public function load(string $player) : Promise;

    /**
     * @param debt $player
     * @return void
     */
    public function save(Debt $player);

    /**
     * @param string $player
     * @return bool
     */
    public function exists(string $player) : bool;
}