<?php

declare(strict_types=1);

namespace EconomySystem\Command;

use EconomySystem\EconomySystem;
use EconomySystem\Service\Exception\MoneyAmountLessThanZeroException;
use EconomySystem\Utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use SmartCommand\command\argument\IntegerArgument;
use SmartCommand\command\argument\PlayerArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;

class SetBalanceCommand extends SmartCommand {
    public function __construct(CommandMessages $messages)
    {
        return parent::__construct
        (
            'setbalance', 
            'set the amount of money in an account', 
            self::DEFAULT_USAGE_PREFIX, 
            ['setmoney'], 
            $messages
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

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
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

                    $sender->sendMessage("Você definiu a o saldo de {$player->getName()} para: $amount coins");
                } catch(MoneyAmountLessThanZeroException $e) {
                    $sender->sendMessage('A quantidade de coins a ser definida não pode ser menor que 0');
                } catch(Exception $e) {
                    EconomySystem::getInstance()->getLogger()->error('Erro desconhecido');
                }
            }
        );
    }
}
