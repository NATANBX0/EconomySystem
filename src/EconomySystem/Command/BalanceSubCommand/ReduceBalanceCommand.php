<?php

declare(strict_types=1);

namespace EconomySystem\Command\BalanceSubCommand;

use EconomySystem\EconomySystem;
use EconomySystem\Service\Exception\MoneyAmountLessThanZeroException;
use EconomySystem\Utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use SmartCommand\command\argument\IntegerArgument;
use SmartCommand\command\argument\PlayerArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\message\CommandMessages;

class ReduceBalanceCommand extends BaseSubCommand {
    public function __construct(SmartCommand $smartCommand)
    {
        return parent::__construct
        (
            $smartCommand,
            'decreasemoney', 
            'decrease the balance from a account',
            ['decrease', 'decreasecoins']
        );
    }

    protected static function getRuntimePermission(): string
    {
        return 'EconomySystem.decrease';
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

        EconomySystem::getInstance()->getEconomyService()->reduceBalance
        (
            $player->getName(),
            $amount
        )->then(
            function($result) use ($amount, $player, $sender) {
                if(!SystemUtils::isValidPlayer($sender))
                {
                    return;
                }
                if(!SystemUtils::isValidPlayer($player))
                {
                    return;
                }

                try {
                    if($result instanceof Exception)
                    {
                        throw $result;
                    }

                    $sender->sendMessage("Saldo da conta de {$player->getName()} diminuido em {$amount} saldo final: $result");
                } catch (MoneyAmountLessThanZeroException $e) {
                    $sender->sendMessage("A quantidade de dinheiro a ser reduzida não pode ser menor que 0");
                } catch (Exception $e) {
                    EconomySystem::getInstance()->getServer()->getLogger()->error("Erro desconhecido: $e");
                }
            }
        );
    }
}