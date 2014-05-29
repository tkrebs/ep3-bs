<?php

namespace Base\Entity;

abstract class AbstractLocaleEntityFactory extends AbstractEntityFactory
{

    /**
     * Adds meta data to the actual entity object.
     *
     * @param AbstractEntity $entity
     * @param mixed $resultRecord
     */
    protected static function setEntityMeta(AbstractEntity $entity, $resultRecord)
    {
        if ($entity instanceof AbstractLocaleEntity) {
            $entity->addMeta($resultRecord->key, $resultRecord->value, $resultRecord->locale);
        }
    }

}