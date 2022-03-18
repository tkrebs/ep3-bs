<?php

namespace Calendar\View\Helper;

use DateTime;
use Zend\View\Helper\AbstractHelper;

class ReservationsForCol extends AbstractHelper
{

    public function __invoke(array &$reservations, DateTime $walkingDate, $walkingTime, $walkingTimeBlock)
    {
        $reservationsForCol = array();

        foreach ($reservations as $rid => $reservation) {

            /*
             * Since reservations are ordered by start time and removed from array after they end,
             * we can just stop searching after conditions do not match anymore.
             */

            if ($reservation->need('date') == $walkingDate->format('Y-m-d') &&
                $reservation->needExtra('time_start_sec') < $walkingTime + $walkingTimeBlock) {

                $reservationsForCol[$rid] = $reservation;

                /* Remove all reservations from array which end in the current time block */

                if ($reservation->needExtra('time_end_sec') <= $walkingTime + $walkingTimeBlock) {
                    unset($reservations[$rid]);
                }
            } else {
                break;
            }
        }

        return $reservationsForCol;
    }

}
