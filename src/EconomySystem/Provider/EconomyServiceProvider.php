<?php

declare(strict_types=1);

namespace EconomySystem\Provider;

use EconomySystem\Data\Account\AccountData;
use EconomySystem\EconomySystem;
use EconomySystem\Service\EconomyService;
use EconomySystem\Utils\Container;

class EconomyServiceProvider implements ProviderInterface
{
    public function register(Container $c)
    {
        $c->singleton(AccountData::class, function(Container $c) {
            return new AccountData(EconomySystem::getInstance()->getDataFolder() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'accounts' . DIRECTORY_SEPARATOR);
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