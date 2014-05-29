<?php

namespace Calendar\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CellLogic extends AbstractHelper
{

    public function __invoke($walkingDate, $walkingTime, $timeBlock, $now, $square, $user, $reservationsForCol)
    {
        return sprintf('<td>%s</td>',
            $this->determineCell($walkingDate, $walkingTime, $timeBlock, $now, $square, $user, $reservationsForCol));
    }

    protected function determineCell($walkingDate, $walkingTime, $timeBlock, $now, $square, $user, $reservationsForCol)
    {
        $view = $this->getView();

        if ($walkingDate <= $now) {
            if (! ($user && $user->can('calendar.see-past'))) {
                return $view->calendarCell('Past', 'cc-over');
            }
        }

        if ($walkingTime < $square->needExtra('time_start_sec') || $walkingTime >= $square->needExtra('time_end_sec')) {
            return $view->calendarCell('Closed', 'cc-over');
        }

        $reservationsForCell = $view->calendarReservationsForCell($reservationsForCol, $square);

        $timeBlockSplit = round($timeBlock / 2);

        if ($timeBlockSplit >= $square->need('time_block_bookable')) {

            $walkingTimeSplit = $walkingTime + $timeBlockSplit;

            $reservationsForFirstHalf = array();
            $reservationsForSecondHalf = array();

            foreach ($reservationsForCell as $rid => $reservation) {
                if ($reservation->needExtra('time_end_sec') <= $walkingTimeSplit || $reservation->needExtra('time_start_sec') < $walkingTimeSplit) {
                    $reservationsForFirstHalf[$rid] = $reservation;
                }

                if ($reservation->needExtra('time_start_sec') >= $walkingTimeSplit || $reservation->needExtra('time_end_sec') > $walkingTimeSplit) {
                    $reservationsForSecondHalf[$rid] = $reservation;
                }
            }

            $firstHalf = $this->renderCell($walkingDate, $walkingTime, $timeBlockSplit, $square, $user, $reservationsForFirstHalf);
            $firstHalfUnified = preg_replace('/ts=[0-9:]{5}\&te=[0-9:]{5}/', '', $firstHalf);

            $walkingDate->modify('+' . $timeBlockSplit . ' sec');

            $secondHalf = $this->renderCell($walkingDate, $walkingTime + $timeBlockSplit, $timeBlockSplit, $square, $user, $reservationsForSecondHalf);
            $secondHalfUnified = preg_replace('/ts=[0-9:]{5}\&te=[0-9:]{5}/', '', $secondHalf);

            $walkingDate->modify('-' . $timeBlockSplit . ' sec');

            if ($firstHalfUnified == $secondHalfUnified) {
                $timeEnd = gmdate('H:i', $walkingTime + $timeBlock);

                if ($timeEnd == '00:00') {
                    $timeEnd = '24:00';
                }

                return preg_replace('/te=[0-9:]{5}/', 'te=' . $timeEnd, $firstHalf);
            } else {
                return sprintf('%s%s',
                    str_replace('calendar-cell', 'calendar-cell cc-height-2', $firstHalf),
                    str_replace('calendar-cell', 'calendar-cell cc-height-2', $secondHalf));
            }
        } else {
            return $this->renderCell($walkingDate, $walkingTime, $timeBlock, $square, $user, $reservationsForCell);
        }
    }

    protected function renderCell($walkingDate, $walkingTime, $timeBlock, $square, $user, $reservationsForCell)
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
            return $this->renderFree($user, $userBooking, $reservationsForCell, $cellLinkParams);
        } else {
            return $this->renderOccupied($user, $userBooking, $reservationsForCell, $cellLinkParams);
        }
    }

    protected function renderFree($user, $userBooking, array $reservations, array $cellLinkParams)
    {
        $view = $this->getView();

        if ($user && $user->can('calendar.see-data, calendar.create-single-bookings, calendar.create-subscription-bookings')) {
            return $this->renderFreeForPrivileged($reservations, $cellLinkParams);
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

    protected function renderFreeForPrivileged(array $reservations, array $cellLinkParams)
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

    protected function renderOccupied($user, $userBooking, array $reservations, array $cellLinkParams)
    {
        $view = $this->getView();

        if ($user && $user->can('calendar.see-data')) {
            return $this->renderOccupiedForPrivileged($reservations, $cellLinkParams);
        } else if ($user) {
            if ($userBooking) {
                $cellLabel = $view->t('Your Booking');
                $cellGroup = ' cc-group-' . $userBooking->need('bid');

                return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), 'cc-own' . $cellGroup);
            } else {
                return $this->renderOccupiedForVisitors($reservations, $cellLinkParams);
            }
        } else {
            return $this->renderOccupiedForVisitors($reservations, $cellLinkParams);
        }
    }

    protected function renderOccupiedForVisitors(array $reservations, array $cellLinkParams)
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

    protected function renderOccupiedForPrivileged(array $reservations, array $cellLinkParams)
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