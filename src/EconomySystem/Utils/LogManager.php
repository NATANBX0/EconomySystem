<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use EconomySystem\EconomySystem;

class LogManager
{
    const PLUGIN_NAME = 'ArenaTops';
    public static function log(string $mensagem)
    {
        $dir = EconomySystem::getInstance()->getDataFolder() . 'logs/';
        if (!is_dir($dir))
            @mkdir($dir, 0777, true);
        $logEntry = "[" . date('H:i:s') . "] ";
        file_put_contents($dir . date('Y-m-d') . '.log', $logEntry . $mensagem . PHP_EOL, FILE_APPEND);
        DevMode::log("[" . self::PLUGIN_NAME . "] " . $mensagem);
    }
}