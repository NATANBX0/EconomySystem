<?php

declare(strict_types=1);

namespace EconomySystem\Task\Async;

use EconomySystem\Model\Account\Account;
use EconomySystem\Utils\Async\AsyncPromiseTask;

class LoadAccountAsyncTask extends AsyncPromiseTask 
{
    public function __construct(string $playerName, string $datafolder)
    {
        return parent::__construct([
            'playerName' => $playerName,
            'datafolder' => $datafolder
        ]);
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        $filePath = $safeVarValues['datafolder'] . $safeVarValues['playerName'] . '.json';
        if(!file_exists($filePath)) {
            $account = new Account($safeVarValues['playerName'], 0);
            file_put_contents($filePath, json_encode($account));
            return serialize($account->jsonSerialize());
        }
        $data = file_get_contents($filePath);
        $jsonData = json_decode($data, true);
        return serialize($jsonData);
    }
}