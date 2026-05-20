<?php

declare(strict_types=1);

namespace EconomySystem\Provider;

use EconomySystem\Utils\Container;

interface ServiceProviderInterface
{
    /**
     * @param Container $c
     * @return void
     */
    public function register(Container $c);
    /**
     * @param Container $c
     * @return void
     */
    public function boot(Container $c);
}