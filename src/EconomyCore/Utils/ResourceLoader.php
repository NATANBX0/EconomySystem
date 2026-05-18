<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use EconomySystem\EconomySystem;

final class ResourceEconomySystem
{

    const DIRECTORY = "directory";

    const FILE = "file";

    /**
     * @param PluginBase $plugin
     * @param string $file
     * @throws \Exception
     * @return void
     */
    public static function init(PluginBase $plugin, string $file)
    {
        if (strpos($file, ".phar") === true) {
            $file = "phar://" . $file;
        }

        $pluginFile = new Config($file . 'plugin.yml', Config::YAML);

        $resources = (array) $pluginFile->get("resources");
        foreach ($resources as $resource) {
            $path = self::withDataFolder($resource);

            $directory = (string) pathinfo($path, PATHINFO_DIRNAME);
            $basename = (string) pathinfo($path, PATHINFO_BASENAME);

            if (!self::isLoaded($directory, self::DIRECTORY)) {
                mkdir($directory, 0777, true);
            }

            if (!self::isLoaded($path, self::FILE) && $basename !== ".") {
                if ($plugin->saveResource($resource) === false)
                    throw new \Exception("File \"{$resource}\" not found in the resources folder");
            }
        }
    }

    public static function withDataFolder(string $file): string
    {
        return EconomySystem::getInstance()->getDataFolder() . $file;
    }

    public static function isLoaded(string $resource, string $type = self::DIRECTORY): bool
    {
        if (is_dir($resource) && $type === self::DIRECTORY) {
            return true;
        }

        if (is_file($resource) && $type === self::FILE) {
            return true;
        }

        return false;
    }
}