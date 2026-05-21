<?php

declare(strict_types=1);

namespace EconomySystem\Provider;

use EconomySystem\Data\Account\AccountData;
use EconomySystem\Data\Debt\DebtData;
use EconomySystem\EconomySystem;
use EconomySystem\Manager\EconomyManager;
use EconomySystem\Service\DebtService;
use EconomySystem\Service\EconomyService;
use EconomySystem\Utils\Container;

class EconomyServiceProvider implements ProviderInterface
{
    public function register(Container $c)
    {
        $c->singleton(AccountData::class, function(Container $c) {
            return new AccountData(EconomySystem::getInstance()->getDataFolder() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'accounts' . DIRECTORY_SEPARATOR);
        });

        $c->singleton(DebtData::class, function(Container $c) {
            return new DebtData(EconomySystem::getInstance()->getDataFolder() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'debts' . DIRECTORY_SEPARATOR);
        });
        
        $c->singleton(DebtService::class, function(Container $c){
            $c->make(DebtData::class);
        });

        $c->singleton(EconomyManager::class, function(Container $c) {
            return new EconomyManager 
            (
                $c->make(EconomyService::class),
                $c->make(DebtService::class)
            );
        });

        $c->singleton(EconomyService::class, function(Container $c) {
            return new EconomyService($c->make(AccountData::class));
        });
    }

    public function boot(Container $c)
    {
        throw new \Exception('Not implemented');
    }
}