<?php

namespace Calendar\View\Helper;

use DateTime;
use Zend\View\Helper\AbstractHelper;

class EventsForCol extends AbstractHelper
{

    public function __invoke(array &$events, DateTime $walkingDate, $walkingTime, $walkingTimeBlock)
    {
        $eventsForCol = array();

        foreach ($events as $eid => $event) {

            /*
             * Since events are ordered by start date and time and removed from array after they end,
             * we can just stop searching after conditions do not match anymore.
             */

            $isToday = $event->needExtra('date_start') == $walkingDate->format('Y-m-d');

            if (! $isToday) {
                $isPrior = $event->needExtra('datetime_start') < $walkingDate;
            } else {
                $isPrior = false;
            }

            $isInTime = $event->needExtra('time_start_sec') < $walkingTime + $walkingTimeBlock;

            if ( ($isToday && $isInTime) || ($isPrior) ) {

                $eventsForCol[$eid] = $event;

                /* Remove all events from array which end in the current time block */

                if ($event->needExtra('date_end') == $walkingDate->format('Y-m-d') &&
                    $event->needExtra('time_end_sec') <= $walkingTime + $walkingTimeBlock) {

                    unset($events[$eid]);
                }
            } else {
                break;
            }
        }

        return $eventsForCol;
    }

}