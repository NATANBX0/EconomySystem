<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use pocketmine\utils\Config;
use ArenaTops\EconomySystem;

class ArenaTopConfigs
{

    const NUMBER_OF_LINES_ON_LEADERBOARD = 'number-of-lines-leaderboard';

    public static function getConfig(): Config
    {
        return EconomySystem::getInstance()->getConfig();
    }

    public static function has($key)
    {
        return self::getConfig()->exists($key);
    }

    public static function get($key, $default = false)
    {
        return self::getConfig()->get($key, $default);
    }

    public static function set($key, $value)
    {
        $config = self::getConfig();
        $config->set($key, $value);
        return $config->save();
    }

    public static function getLeaderboardLinesNumber()
    {
        return self::get(self::NUMBER_OF_LINES_ON_LEADERBOARD, 10);
    }

    public static function showNoScoreArenas()
    {
        return self::get('show-no-score-arenas', true);
    }
}