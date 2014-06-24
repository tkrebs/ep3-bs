<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class FreeForPrivileged extends AbstractHelper
{

    public function __invoke(array $reservations, array $cellLinkParams)
    {
        $view = $this->getView();

        $reservationsCount = count($reservations);

        if ($reservationsCount == 0) {
            return $view->calendarCellLink('Free', $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-free');
        } else if ($reservationsCount == 1) {
            $reservation = current($reservations);
            $booking = $reservation->needExtra('booking');

            $cellLabel = $booking->needExtra('user')->need('alias');
            $cellGroup = ' cc-group-' . $booking->need('bid');

            return $view->calendarCellLink($cellLabel, $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-free cc-free-partially' . $cellGroup);
        } else {
            return $view->calendarCellLink('Still free', $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-free cc-free-partially');
        }
    }

}