<?php

declare(strict_types=1);

namespace EconomySystem\Task\Async;

use EconomySystem\Model\Account\Account;
use EconomySystem\Utils\Async\AsyncPromiseTask;

class SaveAccountAsyncTask extends AsyncPromiseTask
{
    public function __construct(Account $account, string $datafolder)
    {
        return parent::__construct([
            'account' => $account->jsonSerialize(),
            'datafolder' => $datafolder
        ]);
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        $filePath = $safeVarValues['datafolder'] . $safeVarValues['account']['player_name'] . '.json';
        $data = file_put_contents($filePath, json_encode($safeVarValues['account']));
        return serialize(true);
    }
}