<?php

namespace Calendar\View\Helper;

use DateTime;
use Zend\View\Helper\AbstractHelper;

class EventsCleanup extends AbstractHelper
{

    public function __invoke(array &$events, DateTime $walkingDate)
    {
        foreach ($events as $eid => $event) {
            if ($event->needExtra('date_end') == $walkingDate->format('Y-m-d')) {
                unset($events[$eid]);
            } else {
                break;
            }
        }
    }

}