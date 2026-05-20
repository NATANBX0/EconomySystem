<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use EconomySystem\EconomySystem;

class DevMode
{

    public static function log($message)
    {
        EconomySystem::getInstance()->getServer()->getLogger()->info("[DevMode] " . $message);
        return;
    }
}