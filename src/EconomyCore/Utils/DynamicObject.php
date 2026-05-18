<?php

declare(strict_types=1);

/***
 *   
 * Created by:
 * 
 * Rajador: https://github.com/RajadorDev
 * 
 **/

namespace EconomySystem\Utils;

use JsonSerializable;

abstract class DynamicObject implements JsonSerializable
{

    const SOURCE_ID = 'source';

    /**
     * @param array $data
     * @return DynamicObject
     */
    abstract public static function unserialize(array $data): DynamicObject;

    /**
     * Called from jsonSerialize() method
     * @return array<string,mixed>
     */
    abstract protected function serializeExtraData(): array;

    /**
     * @param array{SOURCE_ID:class-string<DynamicObject>} $data
     * @return DynamicObject
     */
    public static function globalUnserialize(array $data): DynamicObject
    {
        return $data[self::SOURCE_ID]::unserialize($data);
    }

    /**
     * @param array{SOURCE_ID:class-string<DynamicObject>}[] $list
     * @return DynamicObject[]
     */
    public static function unserializeAll(array $list): array
    {
        $objects = [];
        foreach ($list as $objectSerialized) {
            $objects[] = static::globalUnserialize($objectSerialized);
        }
        return $objects;
    }

    public function jsonSerialize()
    {
        return array_merge([self::SOURCE_ID => get_class($this)], $this->serializeExtraData());
    }

    /**
     * @param DynamicObject[] $objects
     * @return array
     */
    public static function serializeAll(array $objects): array
    {
        return array_map(
            static function (DynamicObject $obj): array {
                return $obj->jsonSerialize();
            },
            $objects
        );
    }

}