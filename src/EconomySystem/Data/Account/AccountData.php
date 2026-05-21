<?php

declare(strict_types=1);

namespace EconomySystem\Data\Account;

use EconomySystem\Model\Account\Account;
use EconomySystem\Task\Async\LoadAccountAsyncTask;
use EconomySystem\Task\Async\SaveAccountAsyncTask;
use EconomySystem\Utils\Promise\Promise;

class AccountData implements AccountDataInterface
{
    /** @var string */
    protected $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
        if (!is_dir($dataFolder)) {
            @mkdir($dataFolder, 0777, true);
        }
    }

    public function load(string $playerName) : Promise
    {
        $task = new LoadAccountAsyncTask($playerName, $this->dataFolder);
        LoadAccountAsyncTask::schedule($task);
        return $task->getPromise();
    }

    public function save(Account $account)
    {
        $task = new SaveAccountAsyncTask($account, $this->dataFolder);
        SaveAccountAsyncTask::schedule($task);
    }

    public function exists(string $playerName) : bool
    {
        return is_file($this->dataFolder . $playerName . '.json');
    }
}