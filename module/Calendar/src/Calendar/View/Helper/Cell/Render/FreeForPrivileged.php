<?php

namespace Calendar\View\Helper\Cell\Render;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class FreeForPrivileged extends AbstractHelper
{

    public function __invoke(array $reservations, array $cellLinkParams, Square $square)
    {
        $view = $this->getView();

        $reservationsCount = count($reservations);

        if ($reservationsCount == 0) {
	        $labelFree = $square->getMeta('label.free', $this->view->t('Free'));

            return $view->calendarCellLink($labelFree, $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-free');
        } else if ($reservationsCount == 1) {
            $reservation = current($reservations);
            $booking = $reservation->needExtra('booking');

            $cellLabel = $booking->needExtra('user')->need('alias');
            $cellGroup = ' cc-group-' . $booking->need('bid');

            return $view->calendarCellLink($view->escapeHtml($cellLabel), $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-free cc-free-partially' . $cellGroup);
        } else {
	        $labelFree = $square->getMeta('label.free', 'Still free');

            return $view->calendarCellLink($labelFree, $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-free cc-free-partially');
        }
    }

}
