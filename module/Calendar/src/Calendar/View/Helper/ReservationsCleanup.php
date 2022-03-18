<?php

namespace Calendar\View\Helper;

use DateTime;
use Zend\View\Helper\AbstractHelper;

class ReservationsCleanup extends AbstractHelper
{

    public function __invoke(array &$reservations, DateTime $walkingDate)
    {
        foreach ($reservations as $rid => $reservation) {
            if ($reservation->need('date') == $walkingDate->format('Y-m-d')) {
                unset($reservations[$rid]);
            } else {
                break;
            }
        }
    }

}