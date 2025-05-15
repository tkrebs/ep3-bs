<?php

namespace Base\Entity;

use Closure;
use InvalidArgumentException;
use Traversable;

/**
 * Entity factories create entities from different sources.
 */
abstract class AbstractEntityFactory
{

    protected static $entityClass;
    protected static $entityPrimary;

    /**
     * Creates entities from a Traversable, especially from a (database) result set.
     *
     * @param Traversable $resultSet
     * @param Closure|null $closure
     * @return array
     */
    public static function fromResultSet(Traversable $resultSet, ?Closure $closure = null)
    {
        $entities = array();

        $entityClass = static::$entityClass;
        $entityPrimary = static::$entityPrimary;

        foreach ($resultSet as $resultRecord) {
            if (isset($resultRecord->$entityPrimary) && !isset($entities[$resultRecord->$entityPrimary])) {
                $entity = new $entityClass();

                foreach ($resultRecord as $key => $value) {
                    if (property_exists($entity, $key) && !is_null($value)) {
                        $entity->add($key, $value);
                    }
                }

                $entities[$resultRecord->$entityPrimary] = $entity;

                if ($closure) {
                    $closure($entity, $resultRecord);
                }
            }

            if (isset($resultRecord->$entityPrimary) && isset($resultRecord->key) && isset($resultRecord->value) && !is_null($resultRecord->value)) {
                $entity = $entities[$resultRecord->$entityPrimary];

                static::setEntityMeta($entity, $resultRecord);
            }
        }

        return $entities;
    }

    /**
     * Adds meta data from meta queries to the passed entities.
     *
     * @param array $entities
     * @param Traversable $resultSet
     * @throws InvalidArgumentException
     * @return array
     */
    public static function fromMetaResultSet(array $entities, Traversable $resultSet)
    {
        $entityPrimary = static::$entityPrimary;

        foreach ($resultSet as $resultRecord) {
            if (! (isset($resultRecord->$entityPrimary) && isset($resultRecord->key))) {
                throw new InvalidArgumentException('Invalid meta result set passed');
            }

            $entity = $entities[$resultRecord->$entityPrimary];

            static::setEntityMeta($entity, $resultRecord);
        }

        return $entities;
    }

    /**
     * Adds meta data to the actual entity object.
     *
     * @param AbstractEntity $entity
     * @param mixed $resultRecord
     */
    protected static function setEntityMeta(AbstractEntity $entity, $resultRecord)
    {
        $entity->addMeta($resultRecord->key, $resultRecord->value);
    }

}
