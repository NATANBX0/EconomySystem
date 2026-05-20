<?php

declare(strict_types=1);

/***
 *   
 * Created by:
 * 
 * Rajador: https://github.com/RajadorDev
 * 
 **/

namespace EconomySystem\Utils\Promise;

class Promise
{

    /** @var PromiseResolver */
    protected $resolver;

    /** @var callable[] `(mixed) : void` */
    protected $thenCall = [];

    /** @var callable[] `() : void` */
    protected $catchCall = [];

    public function __construct(PromiseResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param callable[] `(mixed) : void`
     * @return Promise
     */
    public function then(callable $callback): Promise
    {
        $this->thenCall[] = $callback;
        if ($this->resolver->isResolved()) {
            $callback($this->resolver->getResult());
        }
        return $this;
    }

    /**
     * @param callable[] `() : void`
     * @return Promise
     */
    public function catch(callable $callback): Promise
    {
        $this->catchCall[] = $callback;
        if ($this->resolver->isErrorCatched()) {
            $callback();
        }
        return $this;
    }

    public function onResolve()
    {
        $result = $this->resolver->getResult();
        foreach ($this->thenCall as $callThen) {
            $callThen($result);
        }
    }

    public function onError()
    {
        foreach ($this->catchCall as $callOnCatch) {
            $callOnCatch();
        }
    }
}