<?php

declare(strict_types=1);

namespace EconomySystem\Provider;

use EconomySystem\Utils\Container;

interface ServiceProviderInterface
{
    public function register(Container $c);
    public function boot(Container $c);
}