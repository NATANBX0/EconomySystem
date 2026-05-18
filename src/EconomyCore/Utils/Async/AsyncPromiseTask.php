<?php

declare(strict_types=1);

/***
 *   
 * Created by:
 * 
 * Rajador: https://github.com/RajadorDev
 * 
 **/


namespace EconomySystem\Utils\Async;

use EconomySystem\Utils\DynamicObject;
use pocketmine\Server;
use Throwable;


use EconomySystem\Utils\Async\RealAsyncTask;
use EconomySystem\Utils\Promise\Promise;
use EconomySystem\Utils\Promise\PromiseResolver;

/**
 * This class will use Promise system and automatically try to converts strings to json_decode
 */
abstract class AsyncPromiseTask extends RealAsyncTask
{

    const VAR_RESOLVER_ID = 'PromiseResolver';

    /** @var string */
    protected $threadSafeValues;

    /** @var string */
    protected $error;

    /**
     * @param array<string,string|int|bool|null> $values
     * @param boolean $checkSafeThreadValues 
     */
    public function __construct(
        array $values,
        bool $checkSafeThreadValues = true
    ) {
        if ($checkSafeThreadValues) {
            self::checkThreadValue($values);
        }
        $this->threadSafeValues = serialize($values);
        $this->saveToThreadStore(self::VAR_RESOLVER_ID, new PromiseResolver);
    }

    public static function checkThreadValue(array $varList)
    {
        foreach ($varList as $index => $value) {
            if (self::isSafeThreadValue($value)) {
                continue;
            }

            if (is_array($value)) {
                self::checkThreadValue($value);
                continue;
            }
        }
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public static function isSafeThreadValue($value): bool
    {
        return (
            is_string($value)
            ||
            is_bool($value)
            ||
            is_int($value)
            ||
            is_null($value)
            ||
            is_float($value)
        );
    }

    protected function getResolver(): PromiseResolver
    {
        return $this->getFromThreadStore(self::VAR_RESOLVER_ID);
    }

    public function getPromise(): Promise
    {
        return $this->getResolver()->getPromise();
    }

    public function onRun()
    {
        try {
            $result = $this->processAndSerializeResult(unserialize($this->threadSafeValues));
            $this->setResult($result);
        } catch (Throwable $error) {
            $this->error = (string) $error;
        }
    }

    /**
     * @param array<string,string|int|bool|float|null|array> $safeVarValues
     * @return mixed
     */
    abstract protected function processAndSerializeResult(array $safeVarValues);

    public function onCompletion(Server $server)
    {
        $resolver = $this->getResolver();
        if (isset($this->error)) {
            $resolver->error($this->error);
            return;
        }

        $result = unserialize($this->getResult());

        if (is_array($result) && isset($result[DynamicObject::SOURCE_ID])) {
            $result = DynamicObject::globalUnserialize($result);
        }

        $resolver->resolve(
            $result
        );
    }

}