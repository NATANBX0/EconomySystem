<?php

declare(strict_types=1);

namespace EconomySystem\Command\BalanceSubCommand;

use EconomySystem\EconomySystem;
use EconomySystem\Service\Exception\MoneyAmountLessThanZeroException;
use EconomySystem\Utils\Messages;
use EconomySystem\Utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use SmartCommand\command\argument\IntegerArgument;
use SmartCommand\command\argument\PlayerArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\message\CommandMessages;

class SetBalanceCommand extends BaseSubCommand {
    public function __construct(SmartCommand $smartCommand)
    {
        return parent::__construct
        (
            $smartCommand,
            'setbalance', 
            'set the amount of money in an account',
            ['setmoney']
        );
    }

    protected static function getRuntimePermission(): string
    {
        return 'EconomySystem.set';
    }

    protected function prepare()
    {
        $this->registerArguments
        ([
            new PlayerArgument('player', PlayerArgument::SEARCH_FROM_PREFIX),
            new IntegerArgument('amount', true)
        ]);
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $player = $args->getPlayer('player');
        $amount = $args->getInteger('amount');
        EconomySystem::getInstance()->getEconomyService()->setBalance(
            $player->getName(),
            $amount
        )->then(
            function($result) use ($player, $sender, $amount) {
                if(!SystemUtils::isValidPlayer($player))
                {
                    return;
                }

                if(!SystemUtils::isValidPlayer($sender))
                {
                    return;
                }

                try {
                    if($result instanceof Exception)
                    {
                        throw $result;
                    }

                    Messages::send($sender, 'setmoney', ['{player}', '{amount}', '{money}'], [$player->getName(), $amount, $result]);
                } catch(MoneyAmountLessThanZeroException $e) {
                    Messages::send($sender, 'amount-less-than-zero');
                } catch(Exception $e) {
                    EconomySystem::getInstance()->getLogger()->error('Erro desconhecido: ' . $e);
                }
            }
        );
    }
}
