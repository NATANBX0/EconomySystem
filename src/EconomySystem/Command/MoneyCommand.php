<?php

declare(strict_types=1);

namespace EconomySystem\Command;

use EconomySystem\Command\BalanceSubCommand\IncreaseBalanceCommand;
use EconomySystem\Command\BalanceSubCommand\ReduceBalanceCommand;
use EconomySystem\Command\BalanceSubCommand\SetBalanceCommand;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;

class MoneyCommand extends SmartCommand {
    public function __construct(CommandMessages $messages)
    {
        return parent::__construct
        (
            'money', 
            'admin money/coin related commands', 
            self::DEFAULT_USAGE_PREFIX, 
            ['coin'], 
            $messages
        );
    }

    protected static function getRuntimePermission(): string
    {
        return 'economysystem.money';
    }

    protected function prepare()
    {
        $this->registerSubCommands
        ([
            new IncreaseBalanceCommand($this),
            new ReduceBalanceCommand($this),
            new SetBalanceCommand($this)
        ]);
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        foreach($this->generateSubCommandsUsages('money', $sender) as $subCommandUsage)
        {
            $sender->sendMessage($subCommandUsage);
        }
    }
}