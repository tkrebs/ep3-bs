<?php

namespace Calendar\View\Helper;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class EventsForCell extends AbstractHelper
{

    public function __invoke(array $eventsForCol, Square $square)
    {
        $eventsForCell = array();

        foreach ($eventsForCol as $eid => $event) {
            $sid = $event->get('sid');

            if ($sid == $square->need('sid') || is_null($sid)) {
                if ($event->need('status') == 'enabled') {
                    $eventsForCell[$eid] = $event;
                }
            }
        }

        return $eventsForCell;
    }

}