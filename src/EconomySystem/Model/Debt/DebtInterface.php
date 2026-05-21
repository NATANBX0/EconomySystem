<?php

declare(strict_types=1);

namespace EconomySystem\Model\Debt;

use pocketmine\Player;

interface DebtInterface
{
    const PLAYER_NAME = 'player_name';
    const DEBTS = 'debts';

    /**
     * @param string $player
     * @return array
     */
    public function getDebts(string $player) : array;

    /**
     * @param string $creditor
     * @return bool
     */
    public function has(string $creditor) : bool;
    /**
     * @param string $creditor
     * @param int $amount
     * @return void
     */
    public function add(string $creditor, int $amount);

    /**
     * @param string $creditor
     * @return void
     */
    public function remove(string $creditor);

    /**
     * @param string $creditor
     * @return int
     */
    public function getDebt(string $creditor) : int;

    /**
     * @param string $creditor
     * @param int $amount
     * @return void
     */
    public function payDebt(string $creditor, int $amount);
}