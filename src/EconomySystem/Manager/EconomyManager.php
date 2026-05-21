<?php

declare(strict_types=1);

namespace EconomySystem\Manager;

use EconomySystem\Service\DebtService;
use EconomySystem\Service\EconomyService;

class EconomyManager
{
    /** @var DebtService */
    protected $economyService;

    /** @var DebtService */
    protected $debtService;

    public function __construct(EconomyService $economyService, DebtService $debtService)
    {
        $this->economyService = $economyService;
        $this->debtService = $debtService;
    }
}