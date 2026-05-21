<?php

declare(strict_types=1);

namespace EconomySystem\Task\Async;

use EconomySystem\Model\Debt\Debt;
use EconomySystem\Utils\Async\AsyncPromiseTask;

class SaveDebtsAsyncTask extends AsyncPromiseTask
{
    public function __construct(Debt $debt, string $datafolder)
    {
        return parent::__construct
        (
            [
                'debt' => $debt->jsonSerialize(),
                'datafolder' => $datafolder
            ], 
            true
        );
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        $debt = $safeVarValues['debt'];
        $filePath = $safeVarValues['datafolder'] . $debt[Debt::PLAYER_NAME] . '.json';
        file_put_contents($filePath, json_encode($filePath));
        return serialize(true);
    }
}