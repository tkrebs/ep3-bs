<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class Free extends AbstractHelper
{

    public function __invoke($user, $userBooking, array $reservations, array $cellLinkParams)
    {
        $view = $this->getView();

        if ($user && $user->can('calendar.see-data, calendar.create-single-bookings, calendar.create-subscription-bookings')) {
            return $view->calendarCellRenderFreeForPrivileged($reservations, $cellLinkParams);
        } else if ($user) {
            if ($userBooking) {
                $cellLabel = $view->t('Your Booking');
                $cellGroup = ' cc-group-' . $userBooking->need('bid');

                return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), 'cc-own' . $cellGroup);
            } else {
                return $view->calendarCellLink('Free', $view->url('square', [], $cellLinkParams), 'cc-free');
            }
        } else {
            return $view->calendarCellLink('Free', $view->url('square', [], $cellLinkParams), 'cc-free');
        }
    }

}