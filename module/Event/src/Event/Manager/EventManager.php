<?php

namespace Event\Manager;

use Base\Entity\AbstractEntity;
use Base\Manager\AbstractLocaleEntityManager;
use Event\Entity\EventFactory;
use RuntimeException;
use Traversable;
use Zend\Db\Sql\Where;

class EventManager extends AbstractLocaleEntityManager
{

    protected function getInsertValues(AbstractEntity $entity)
    {
        return array(
            'sid' => $entity->get('sid'),
            'status' => $entity->get('status', 'enabled'),
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

    /**
     * Gets all events within the specified datetime interval.
     *
     * Events are ordered by start date and time.
     *
     * @param \DateTime $dateTimeStart
     * @param \DateTime $dateTimeEnd
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getInRange(\DateTime $dateTimeStart, \DateTime $dateTimeEnd,
        $limit = null, $offset = null, $loadMeta = true)
    {
        $where = new Where();

        $where->greaterThan('datetime_end', $dateTimeStart->format('Y-m-d H:i:s'));
        $where->and;
        $where->lessThan('datetime_start', $dateTimeEnd->format('Y-m-d H:i:s'));

        return $this->getBy($where, 'datetime_start ASC', $limit, $offset, $loadMeta);
    }

    /**
     * Calculates the passed seconds per day for each event and saves the result
     * as extra key 'time_start_sec' and 'time_end_sec'.
     *
     * Creates DateTime objects for the dates and times and saves the result
     * as extra key 'datetime_start' and 'datetime_end'.
     *
     * @param array $events
     * @return array
     */
    public function getSecondsPerDay($events)
    {
        if (! is_array($events)) {
            $events = array($events);
        }

        foreach ($events as $event) {
            $dateTimeStart = new \DateTime($event->need('datetime_start'));

            $timeStartParts = explode(':', $dateTimeStart->format('H:i'));
            $timeStartSec = $timeStartParts[0] * 3600 + $timeStartParts[1] * 60;

            $event->setExtra('time_start_sec', $timeStartSec);
            $event->setExtra('datetime_start', $dateTimeStart);
            $event->setExtra('date_start', $dateTimeStart->format('Y-m-d'));

            $dateTimeEnd = new \DateTime($event->need('datetime_end'));

            $timeEndParts = explode(':', $dateTimeEnd->format('H:i'));
            $timeEndSec = $timeEndParts[0] * 3600 + $timeEndParts[1] * 60;

            if ($timeEndSec == 0) {
                $timeEndSec = 86400;
            }

            $event->setExtra('time_end_sec', $timeEndSec);
            $event->setExtra('datetime_end', $dateTimeEnd);
            $event->setExtra('date_end', $dateTimeEnd->format('Y-m-d'));
        }

        return $events;
    }

}