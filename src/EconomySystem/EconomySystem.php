<?php

declare(strict_types=1);

namespace EconomySystem;

use EconomySystem\Command\AddMoneyCommand;
use EconomySystem\Command\MyMoneyCommand;
use EconomySystem\Command\ReduceBalanceCommand;
use EconomySystem\Command\SetBalanceCommand;
use EconomySystem\Command\TransferMoneyCommand;
use EconomySystem\Provider\EconomyServiceProvider;
use EconomySystem\Service\EconomyService;
use EconomySystem\Utils\Container;
use EconomySystem\Utils\Messages;
use EconomySystem\Utils\ResourceEconomySystem;
use EconomySystem\Utils\ResourceLoader;
use EconomySystem\Utils\SingletonTrait;
use pocketmine\plugin\PluginBase;
use SmartCommand\message\DefaultMessages;

class EconomySystem extends PluginBase {
    use SingletonTrait;

    /**
     * @var EconomyService
     */
    private $economyService;

    public function onEnable()
    {
        self::setInstance($this);
        Messages::init();
        ResourceLoader::init($this, $this->getFile());
        $this->bootProviders();
        $this->registerCommands();
        $this->getServer()->getPluginManager()->registerEvents(new ESListener(), $this);
    }

    public function bootProviders()
    {
        $economyProvider = new EconomyServiceProvider();
        $container = new Container();
        $economyProvider->register($container);
        $this->economyService = $container->make(EconomyService::class);
    }

    public function getEconomyService() : EconomyService
    {
        return $this->economyService;
    }

    public function registerCommands()
    {
        $cm = $this->getServer()->getCommandMap();
        $messages = DefaultMessages::PORTUGUESE();
        $messages->add(Messages::getConfig()->getAll());
        $cm->register('mymoney', new MyMoneyCommand($messages));
        $cm->register('pay', new TransferMoneyCommand($messages));
        $cm->register('setbalance', new SetBalanceCommand($messages));
        $cm->register('addmoney', new AddMoneyCommand($messages));
        $cm->register('reducebalance', new ReduceBalanceCommand($messages));
    }
}