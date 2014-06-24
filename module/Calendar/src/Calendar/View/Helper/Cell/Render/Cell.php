<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class Cell extends AbstractHelper
{

    public function __invoke($walkingDate, $walkingTime, $timeBlock, $square, $user, $reservationsForCell, $eventsForCell)
    {
        $view = $this->getView();

        $cellLinkParams = ['query' => [
            'ds' => $walkingDate->format('Y-m-d'),
            'ts' => gmdate('H:i', $walkingTime),
            'te' => gmdate('H:i', $walkingTime + $timeBlock),
            's' => $square->need('sid'),
        ]];

        if ($cellLinkParams['query']['te'] == '00:00') {
            $cellLinkParams['query']['te'] = '24:00';
        }

        $capacity = $square->need('capacity');
        $capacityHeterogenic = $square->need('capacity_heterogenic');

        $quantity = 0;

        /* Check events */

        if ($eventsForCell) {
            if ($user && $user->can('calendar.see-data')) {
                if ($eventsForCell && $reservationsForCell) {
                    return $view->calendarCellLink('Conflict', $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-conflict');
                }

                if (count($eventsForCell) > 1) {
                    return $view->calendarCellLink('Conflict', $view->url('backend/event/edit', [], $cellLinkParams), 'cc-conflict');
                }
            }

            $event = current($eventsForCell);

            return $view->calendarCellRenderEvent($event, $cellLinkParams);
        }

        /* Check bookings */

        $userBooking = null;

        foreach ($reservationsForCell as $reservation) {
            $booking = $reservation->needExtra('booking');
            $quantity += $booking->need('quantity');

            if ($user && $user->need('uid') == $booking->need('uid')) {
                $userBooking = $booking;
            }
        }

        if ($capacity > $quantity) {
            if ($quantity && ! $capacityHeterogenic) {
                $cellFree = false;
            } else {
                $cellFree = true;
            }
        } else {
            $cellFree = false;
        }

        if ($capacity - $quantity < 0) {
            if ($user && $user->can('calendar.see-data')) {
                return $view->calendarCellLink('Conflict', $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-conflict');
            }
        }

        if ($cellFree) {
            return $view->calendarCellRenderFree($user, $userBooking, $reservationsForCell, $cellLinkParams);
        } else {
            return $view->calendarCellRenderOccupied($user, $userBooking, $reservationsForCell, $cellLinkParams);
        }
    }

}