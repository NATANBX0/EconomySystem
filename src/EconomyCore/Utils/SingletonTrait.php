<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

trait SingletonTrait
{

    /** @var null|self */
    private static $instance = null;

    public static function setInstance(self $instance)
    {
        self::$instance = $instance;
    }

    public static function make(): self
    {
        /**@disregard */
        return new self();
    }

    public static function getInstance(): self
    {
        if (!self::$instance === null) {
            self::$instance = self::make();
        }

        return self::$instance;
    }
}