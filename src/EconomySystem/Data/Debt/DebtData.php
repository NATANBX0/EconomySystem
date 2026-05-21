<?php

declare(strict_types=1);

namespace EconomySystem\Data\Debt;

use EconomySystem\Model\AccountDebt\DebtInterface;
use EconomySystem\Model\Debt\Debt;
use EconomySystem\Task\Async\LoadDebtsAsyncTask;
use EconomySystem\Task\Async\SaveDebtsAsyncTask;
use EconomySystem\Utils\Promise\Promise;
use EconomySystem\Utils\Promise\PromiseResolver;

class DebtData implements DebtDataInterface
{
    /** @var string */
    private $datafolder;
    public function __construct(string $datafolder)
    {
        $this->datafolder = $datafolder;
        if(!is_dir($datafolder))
        {
            @mkdir($datafolder, 0777, true);
        }

    }
    public function load(string $player) : Promise
    {
        $task = new LoadDebtsAsyncTask($player, $this->datafolder);
        LoadDebtsAsyncTask::schedule($task);
        return $task->getPromise();
    }

    public function save(Debt $debt)
    {
        $task = new SaveDebtsAsyncTask($debt, $this->datafolder);
        SaveDebtsAsyncTask::schedule($task);
    }

    public function exists(string $player): bool
    {
        return is_file($this->datafolder . $player . '.json');
    }
}