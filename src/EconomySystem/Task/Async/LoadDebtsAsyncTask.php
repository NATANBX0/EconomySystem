<?php

declare(strict_types=1);

namespace EconomySystem\Task\Async;

use EconomySystem\Model\Debt\Debt;
use EconomySystem\Utils\Async\AsyncPromiseTask;

class LoadDebtsAsyncTask extends AsyncPromiseTask {
    public function __construct(string $playerName,string $filePath)
    {
        return parent::__construct(
            [
                'player_name' => $playerName,
                'filepath' => $filePath
            ],
            true
        );
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        $filepath = $safeVarValues['filepath'] . $safeVarValues['player_name'] . '.json';
        if(is_file($filepath))
        {
            $data = file_get_contents($filepath);
            $jsonData = json_decode($data, true);
            return serialize($jsonData);
        }
        $debts = new Debt($safeVarValues['player_name'], []);
        file_put_contents($filepath, json_encode($debts));
        return serialize($debts->jsonSerialize());
    }
}