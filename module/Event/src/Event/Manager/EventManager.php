<?php

namespace Event\Manager;

use Base\Entity\AbstractEntity;
use Base\Manager\AbstractLocaleEntityManager;
use Event\Entity\EventFactory;
use RuntimeException;
use Traversable;

class EventManager extends AbstractLocaleEntityManager
{

    protected function getInsertValues(AbstractEntity $entity)
    {
        return array(
            'sid' => $entity->get('sid'),
            'status' => $entity->get('status'),
            'datetime_start' => $entity->need('datetime_start'),
            'datetime_end' => $entity->need('datetime_end'),
            'capacity' => $entity->get('capacity'),
        );
    }

    protected function getByResultSet(Traversable $resultSet)
    {
        return EventFactory::fromResultSet($resultSet);
    }

    protected function getByMetaResultSet(Traversable $metaResultSet, array $events)
    {
        return EventFactory::fromMetaResultSet($events, $metaResultSet);
    }

    public function get($eid, $strict = true)
    {
        $event = $this->getBy(array('eid' => $eid));

        if (empty($event)) {
            if ($strict) {
                throw new RuntimeException('This event does not exist');
            }

            return null;
        } else {
            return current($event);
        }
    }

}