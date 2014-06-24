<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class OccupiedForPrivileged extends AbstractHelper
{

    public function __invoke(array $reservations, array $cellLinkParams)
    {
        $view = $this->getView();

        $reservationsCount = count($reservations);

        if ($reservationsCount > 1) {
            return $view->calendarCellLink('Occupied', $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-single');
        } else {
            $reservation = current($reservations);
            $booking = $reservation->needExtra('booking');

            $cellLabel = $booking->needExtra('user')->need('alias');
            $cellGroup = ' cc-group-' . $booking->need('bid');

            switch ($booking->need('status')) {
                case 'single':
                    return $view->calendarCellLink($cellLabel, $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-single' . $cellGroup);
                case 'subscription':
                    return $view->calendarCellLink($cellLabel, $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-multiple' . $cellGroup);
            }
        }
    }

}