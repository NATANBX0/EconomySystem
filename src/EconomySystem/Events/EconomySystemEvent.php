<?php

declare(strict_types=1);

namespace EconomySystem\Events;

use EconomySystem\EconomySystem;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\plugin\Plugin;

class EconomySystemEvent extends PluginEvent {

    /** @var string */
    private $origin;

    public function __construct(EconomySystem $plugin, string $origin)
    {
        $this->origin = $origin;
        parent::__construct($plugin);
    }

    public function getOrigin()
    {
        return $this->origin;
    }
}