<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class OccupiedForVisitors extends AbstractHelper
{

    public function __invoke(array $reservations, array $cellLinkParams)
    {
        $view = $this->getView();

        $reservationsCount = count($reservations);

        if ($reservationsCount > 1) {
            return $view->calendarCellLink('Occupied', $view->url('square', [], $cellLinkParams), 'cc-single');
        } else {
            $reservation = current($reservations);
            $booking = $reservation->needExtra('booking');

            $cellGroup = ' cc-group-' . $booking->need('bid');

            switch ($booking->need('status')) {
                case 'single':
                    return $view->calendarCellLink('Occupied', $view->url('square', [], $cellLinkParams), 'cc-single' . $cellGroup);
                case 'subscription':
                    return $view->calendarCellLink('Subscription', $view->url('square', [], $cellLinkParams), 'cc-multiple' . $cellGroup);
            }
        }
    }

}