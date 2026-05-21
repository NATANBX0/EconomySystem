<?php

declare(strict_types=1);

namespace EconomySystem\Command;

use EconomySystem\EconomySystem;
use EconomySystem\Service\Exception\MoneyAmountLessThanZeroException;
use EconomySystem\Utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class MyMoneyCommand extends SmartCommand {
    public function __construct(CommandMessages $messages)
    {
        return parent::__construct
        (
            'mymoney',
            'see ur money',
            self::DEFAULT_USAGE_PREFIX,
            ['mycoins'],
            $messages
        );
    }

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->registerRule(new OnlyInGameCommandRule());
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        EconomySystem::getInstance()->getEconomyService()->getBalance
        (
            $sender->getName()
        )->then(
            function ($result) use ($sender) {
                if(!SystemUtils::isValidPlayer($sender))
                {
                    return;
                }
                try {
                    if($result instanceof Exception)
                    {
                        throw $result;
                    }
                    $sender->sendMessage('Dinheiro na sua conta: ' . $result);
                } catch (Exception $e) {
                    Server::getInstance()->getLogger()->error('Ocorreu um erro desconhecido: ' . $e);
                }
        });
    }
}