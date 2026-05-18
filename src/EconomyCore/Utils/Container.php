<?php

declare(strict_types=1);

namespace EconomySystem\Utils;

use Exception;

class Container
{
    private $bindings = [];
    private $instances = [];

    public function singleton(string $abstract, callable $factory)
    {
        $this->bindings[$abstract] = $factory;
    }

    /**
     * @param string $abstract
     * @throws Exception
     */
    public function make(string $abstract)
    {
        if (!isset($this->bindings[$abstract])) 
        {
            throw new Exception("no bindings for $abstract");
        }

        if (!isset($this->instances[$abstract])) 
        {
            return $this->instances[$abstract] = ($this->bindings[$abstract])($this);
        }

        return $this->instances[$abstract];
    }
}
