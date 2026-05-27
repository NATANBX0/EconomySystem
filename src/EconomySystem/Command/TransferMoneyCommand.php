<?php

declare(strict_types=1);

namespace EconomySystem\Command;

use EconomySystem\EconomySystem;
use EconomySystem\Service\Exception\AmountToTransferHigherThanBalance;
use EconomySystem\Service\Exception\MoneyAmountLessThanZeroException;
use EconomySystem\Utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use SmartCommand\command\argument\IntegerArgument;
use SmartCommand\command\argument\PlayerArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class TransferMoneyCommand extends SmartCommand {
    public function __construct(CommandMessages $messages)
    {
        return parent::__construct
        (
            'pay', 
            'transfer your money to someone', 
            self::DEFAULT_USAGE_PREFIX, 
            ['transfer', 'pagar'], 
            $messages
        );
    }

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->registerArguments
        ([
            new PlayerArgument('player', PlayerArgument::SEARCH_FROM_PREFIX),
            new IntegerArgument('amount', true)
        ]);
        $this->registerRule(new OnlyInGameCommandRule());
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $player = $args->getPlayer('player');
        $amount = $args->getInteger('amount');
        EconomySystem::getInstance()->getEconomyService()->transfer(
            $sender,
            $player,
            $amount
        )->then(
            function ($result) use ($sender, $player, $amount) {
                if(!SystemUtils::isValidPlayer($player)) 
                {
                    return;
                }
                if(!SystemUtils::isValidPlayer($sender)) 
                {
                    return;
                }

                try {
                    if($result instanceof Exception) {
                        throw $result;
                    }
                    if($result)
                    {
                        $sender->sendMessage("Você enviou para {$player->getName()}: $amount coins");
                        $player->sendMessage("Você recebeu de {$sender->getName()}: $amount coins");
                    }
                } catch(MoneyAmountLessThanZeroException $e){
                    $sender->sendMessage("A quantidade de dinheiro não pode ser menor que 0");
                } catch(AmountToTransferHigherThanBalance $e) {
                    $sender->sendMessage("A quantidade de dinheiro a ser enviada é maior que a quantidade disponivel na sua conta");
                } catch (Exception $e){
                    EconomySystem::getInstance()->getLogger()->error('Unknow Error: ' . $e);
                }
            }
        );
    }
}