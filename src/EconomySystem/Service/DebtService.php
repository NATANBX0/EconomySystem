<?php

declare(strict_types=1);

namespace EconomySystem\Service;

use EconomySystem\Data\Account\AccountDataInterface;
use EconomySystem\Data\Debt\DebtDataInterface;
use EconomySystem\Model\Debt\DebtInterface;
use EconomySystem\Utils\Promise\Promise;

class DebtService {
    /** @var AccountDataInterface */
    protected $data;

    /** @var DebtInterface[] */
    private $cache = [];

    public function __construct(DebtDataInterface $data)
    {
        $this->data = $data;
    }

    public function getDebts(string $playerName) : DebtInterface
    {
        $name = strtolower($playerName);
        if(isset($this->cache[$name]))
        {
            return $this->cache[$name];
        }

        $this->data->load($name)->then
        (
            function (DebtInterface $debt) use ($name)
            {
                $this->cache[$name] = $debt;
            }
        );
        return $this->cache[$name];
    }

    public function addDebt(string $debtor, string $creditor, int $amount)
    {
        $this->getDebts($debtor)->add($creditor, $amount);
    }

    public function payDebt(string $debtor, string $creditor, int $amount)
    {
        $this->getDebts($debtor)->payDebt($creditor, $amount);
    }

    public function removeDebt(string $debtor, string $creditor)
    {
        $this->getDebts($debtor)->remove($creditor);
    }
}