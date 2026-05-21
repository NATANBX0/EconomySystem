<?php

declare(strict_types=1);

namespace EconomySystem\Model\Debt;

use EconomySystem\Model\Debt\DebtInterface;
use EconomySystem\Utils\DynamicObject;

class Debt extends DynamicObject implements DebtInterface
{
    /** @var string */
    protected $playerName;
    /** @var array */
    protected $debts;

    public function __construct(string $playerName, array $debts)
    {
        $this->playerName = $playerName;
        $this->debts = $debts;
    }

    public function getDebts(string $player): array
    {
        return $this->debts;
    }

    public function has(string $creditor): bool
    {
        return isset($this->debts[$creditor]);
    }

    public function add(string $creditor, int $amount)
    {
        $this->debts[$creditor] = $amount;
    }

    public function remove(string $creditor)
    {
        unset($this->debts[$creditor]);
    }

    public function getDebt(string $creditor): int
    {
        return $this->debts[$creditor];
    }

    public function payDebt(string $creditor, int $amount)
    {
        $this->debts[$creditor] -= $amount;
    }

    protected function serializeExtraData(): array
    {
        return [
            self::PLAYER_NAME => $this->playerName,
            self::DEBTS => $this->debts
        ];
    }

    public static function unserialize(array $data): DynamicObject
    {
        return new self(
            $data[self::PLAYER_NAME],
            $data[self::DEBTS]
        );
    }
}