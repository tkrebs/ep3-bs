<?php

namespace Calendar\View\Helper\Cell\Render;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class Occupied extends AbstractHelper
{

    public function __invoke($user, $userBooking, array $reservations, array $cellLinkParams, Square $square)
    {
        $view = $this->getView();

        if ($user && $user->can('calendar.see-data')) {
            return $view->calendarCellRenderOccupiedForPrivileged($reservations, $cellLinkParams);
        } else if ($user) {
            if ($userBooking) {
                $cellLabel = $view->t('Your Booking');
                $cellGroup = ' cc-group-' . $userBooking->need('bid');

                return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), 'cc-own' . $cellGroup);
            } else {
                return $view->calendarCellRenderOccupiedForVisitors($reservations, $cellLinkParams, $square);
            }
        } else {
            return $view->calendarCellRenderOccupiedForVisitors($reservations, $cellLinkParams, $square);
        }
    }

}
