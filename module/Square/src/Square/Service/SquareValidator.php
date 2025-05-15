<?php

namespace Square\Service;

use Base\Manager\OptionManager;
use Base\Service\AbstractService;
use Booking\Entity\Booking;
use Booking\Manager\BookingManager;
use Booking\Manager\ReservationManager;
use DateTime;
use Event\Manager\EventManager;
use Exception;
use RuntimeException;
use Square\Manager\SquareManager;
use User\Manager\UserSessionManager;

class SquareValidator extends AbstractService
{

    protected $bookingManager;
    protected $reservationManager;
    protected $eventManager;
    protected $squareManager;
    protected $optionManager;
    protected $user;

    public function __construct(BookingManager $bookingManager, ReservationManager $reservationManager,
        EventManager $eventManager, SquareManager $squareManager, UserSessionManager $userSessionManager,
        OptionManager $optionManager)
    {
        $this->bookingManager = $bookingManager;
        $this->reservationManager = $reservationManager;
        $this->eventManager = $eventManager;
        $this->squareManager = $squareManager;
        $this->optionManager = $optionManager;
        $this->user = $userSessionManager->getSessionUser();
    }

    /**
     * Checks if the passed datetime range and square is valid and within allowed parameters.
     *
     * @param string $dateStart
     * @param string $dateEnd
     * @param string $timeStart
     * @param string $timeEnd
     * @param int $square
     * @return array
     * @throws RuntimeException
     */
    public function isValid($dateStart, $dateEnd, $timeStart, $timeEnd, $square)
    {
        /* Validate square */

        $square = $this->squareManager->get($square);

        if ($square->need('status') == 'disabled') {
            if (! ($this->user && $this->user->can('calendar.create-single-bookings, calendar.create-subscription-bookings'))) {
                throw new RuntimeException('This square is currently not available');
            }
        }

        /* Validate start date */

        try {
            $dateStart = new DateTime($dateStart);
        } catch (Exception $e) {
            throw new RuntimeException('The passed start date is invalid');
        }

        /* Validate end date */

        if ($dateEnd) {
            try {
                $dateEnd = new DateTime($dateEnd);
            } catch (Exception $e) {
                throw new RuntimeException('The passed end date is invalid');
            }
        } else {
            $dateEnd = clone $dateStart;
        }

        /* Validate start time */

        if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeStart)) {
            throw new RuntimeException('The passed start time is invalid');
        }

        $timeStartParts = explode(':', $timeStart);

        $timeStart = clone $dateStart;
        $timeStart->setTime($timeStartParts[0], $timeStartParts[1]);

        /* Validate end time */

        if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeEnd)) {
            throw new RuntimeException('The passed end time is invalid');
        }

        $timeEndParts = explode(':', $timeEnd);

        $timeEnd = clone $dateEnd;
        $timeEnd->setTime($timeEndParts[0], $timeEndParts[1]);

        if ($timeStart >= $timeEnd) {
            throw new RuntimeException('The passed time range is invalid');
        }

        /* Validate time range */

        $dateMin = new DateTime();

        if ($square->get('min_range_book', 0) == 0) {
            $dateMin->modify('-' . ($square->get('time_block_bookable') / 2) . ' sec');
        } else {
            $dateMin->modify('+' . $square->get('min_range_book', 0) . ' sec');
        }

        $dateMax = new DateTime();
        $dateMax->modify('+' . $square->get('range_book', 0) . ' sec');

        if ($timeStart < $dateMin) {
            if (! ($this->user && $this->user->can('calendar.see-past'))) {

                // Allow assist users with calendar.see-data privilege to see the entire day
                if (! ($this->user && $this->user->can('calendar.see-data') && $dateEnd->format('Y-m-d') == $dateMin->format('Y-m-d'))) {
                    throw new RuntimeException('The passed time is already over');
                }
            }
        }

        if ($square->get('min_range_book')) {
            if ($timeStart < $dateMin) {
                if (! ($this->user && $this->user->can('calendar.create-single-bookings, calendar.create-subscription-bookings'))) {
                    throw new RuntimeException('Dieses Datum ist zu kurzfristig');
                }
            }
        }

        if ($square->get('range_book')) {
            if ($timeStart > $dateMax) {
                if (! ($this->user && $this->user->can('calendar.create-single-bookings, calendar.create-subscription-bookings'))) {
                    throw new RuntimeException('The passed date is still too far away');
                }
            }
        }

        /* Validate square time range */

        $squareTimeStartParts = explode(':', $square->need('time_start'));
        $squareTimeStart = clone $timeStart;
        $squareTimeStart->setTime($squareTimeStartParts[0], $squareTimeStartParts[1], $squareTimeStartParts[2]);

        $squareTimeEndParts = explode(':', $square->need('time_end'));
        $squareTimeEnd = clone $timeEnd;
        $squareTimeEnd->setTime($squareTimeEndParts[0], $squareTimeEndParts[1], $squareTimeEndParts[2]);

        if ($timeStart < $squareTimeStart || $timeEnd > $squareTimeEnd) {
            throw new RuntimeException('The passed time range is invalid');
        }

        /* Validate square time block bookable */

        $timeBlockBookable = $square->get('time_block_bookable');

        if ($timeBlockBookable) {
            $timeBlockRequested = $timeEnd->getTimestamp() - $timeStart->getTimestamp();

            if ($timeBlockRequested < $timeBlockBookable) {
                $timeEnd->modify('+' . ($timeBlockBookable - $timeBlockRequested) . ' seconds');
            }

            $timeBlockDays = $timeEnd->format('z') - $timeStart->format('z');

            if ($timeBlockDays > 0) {
                $idleSquareTimeStart = $squareTimeStartParts[0] * 3600 + $squareTimeStartParts[1] * 60;
                $idleSquareTimeEnd = 86400 - ($squareTimeEndParts[0] * 3600 + $squareTimeEndParts[1] * 60);

                $timeBlockRequested -= ($idleSquareTimeStart + $idleSquareTimeEnd) * $timeBlockDays;
            }

            /* Validate square time block maximum */

            $squareTimeBlockMax = $square->get('time_block_bookable_max');

            if ($squareTimeBlockMax) {
                if ($squareTimeBlockMax < $timeBlockRequested) {
                    if (! ($this->user && $this->user->can('calendar.create-single-bookings, calendar.create-subscription-bookings'))) {
                        $squareTimeBlockMaxRound = round($squareTimeBlockMax / 60);

                        throw new RuntimeException(sprintf($this->t('You cannot book more than %s minutes at once'), $squareTimeBlockMaxRound));
                    }
                }
            }
        }

        /* Check for day exception */

        $dayExceptions = $this->optionManager->get('service.calendar.day-exceptions');

        if ($dayExceptions) {
            $dayExceptions = preg_split('~(\\n|,)~', $dayExceptions);
            $dayExceptionsExceptions = [];

            $dayExceptionsCleaned = [];

            foreach ($dayExceptions as $dayException) {
                $dayException = trim($dayException);

                if ($dayException) {
                    if ($dayException[0] === '+') {
                        $dayExceptionsExceptions[] = trim($dayException, '+');
                    } else {
                        $dayExceptionsCleaned[] = $dayException;
                    }
                }
            }

            $dayExceptions = $dayExceptionsCleaned;

            if (in_array($dateStart->format($this->t('Y-m-d')), $dayExceptions) ||
                in_array($this->t($dateStart->format('l')), $dayExceptions)) {

                if (! in_array($dateStart->format($this->t('Y-m-d')), $dayExceptionsExceptions)) {
                    throw new RuntimeException('The passed date has been hidden from the calendar');
                }
            }
        }

        /* Return validation byproducts */

        return array(
            'dateStart' => $timeStart,
            'dateEnd' => $timeEnd,
            'square' => $square,
            'user' => $this->user,
        );
    }

    /**
     * Checks if the passed datetime range and square is valid and within allowed parameters.
     * Checks if the passed datetime range can be booked by the user.
     *
     * @param string $dateStart
     * @param string $dateEnd
     * @param string $timeStart
     * @param string $timeEnd
     * @param int $square
     * @return array
     */
    public function isBookable($dateStart, $dateEnd, $timeStart, $timeEnd, $square)
    {
        $byproducts = $this->isValid($dateStart, $dateEnd, $timeStart, $timeEnd, $square);

        $dateStart = $byproducts['dateStart'];
        $dateEnd = $byproducts['dateEnd'];
        $square = $byproducts['square'];
        $user = $byproducts['user'];

        $notBookableReason = null;

        /* Check for other reservations */

        $possibleReservations = $this->reservationManager->getInRange($dateStart, $dateEnd);
        $possibleBookings = $this->bookingManager->getByReservations($possibleReservations);

        $reservations = array();
        $bookings = array();

        $quantity = 0;

        $bookingsFromUser = array();

        foreach ($possibleBookings as $bid => $booking) {
            if ($booking->need('sid') == $square->need('sid')) {
                if ($booking->need('visibility') == 'public') {
                    if ($booking->need('status') != 'cancelled') {
                        $bookings[$bid] = $booking;
                        $quantity += $booking->need('quantity');

                        if ($user && $user->need('uid') == $booking->need('uid')) {
                            $bookingsFromUser[$bid] = $booking;
                        }
                    }
                }
            }
        }

        if ($bookings) {
            foreach ($possibleReservations as $rid => $reservation) {
                if (isset($bookings[$reservation->need('bid')])) {
                    $reservations[$rid] = $reservation;
                }
            }
        }

        $capacity = $square->need('capacity');
        $capacityHeterogenic = $square->need('capacity_heterogenic');

        if ($capacity > $quantity) {
            if ($quantity && ! $capacityHeterogenic) {
                $bookable = false;
            } else {
                $bookable = true;
            }
        } else {
            $bookable = false;
        }

        /* Check for maximum active bookings limitation */

        if ($user) {
            $maxActiveBookings = $square->need('max_active_bookings');

            if ($maxActiveBookings != 0) {
                $activeBookings = $this->bookingManager->getByValidity([
                    'uid' => $user->need('uid'),
                ]);

                $this->reservationManager->getByBookings($activeBookings);

                $activeBookingsCount = 0;

                foreach ($activeBookings as $activeBooking) {
                    foreach ($activeBooking->getExtra('reservations') as $activeReservation) {
                        $activeReservationDate = new DateTime($activeReservation->get('date') . ' ' . $activeReservation->get('time_start'));

                        if ($activeReservationDate > new DateTime()) {
                            $activeBookingsCount++;
                        }
                    }
                }

                if ($activeBookingsCount >= $maxActiveBookings) {
                    $bookable = false;
                    $notBookableReason = 'Sie können derzeit nur <b>' . $maxActiveBookings . ' aktive Buchung/en</b> gleichzeitig offen haben.';
                }
            }
        }

        /* Check for blocking events */

        $events = $this->eventManager->getInRange($dateStart, $dateEnd);

        foreach ($events as $event) {
            if (is_null($event->get('sid')) || $event->get('sid') == $square->need('sid')) {
                $bookable = false;
            }
        }

        /* Gather byproducts */

        $byproducts['bookings'] = $bookings;
        $byproducts['bookingsFromUser'] = $bookingsFromUser;
        $byproducts['reservations'] = $reservations;
        $byproducts['bookable'] = $bookable;
        $byproducts['notBookableReason'] = $notBookableReason;
        $byproducts['quantity'] = $quantity;
        $byproducts['events'] = $events;

        return $byproducts;
    }

    /**
     * Checks if the current user is allowed to cancel this booking.
     *
     * @param Booking $booking
     * @return boolean
     * @throws RuntimeException
     */
    public function isCancellable(Booking $booking)
    {
        if ($this->user && $this->user->can('calendar.cancel-single-bookings')) {
            if ($booking->need('status') == 'single') {
                return true;
            }
        }

        if ($this->user && $this->user->can('calendar.cancel-subscription-bookings')) {
            if ($booking->need('status') == 'subscription') {
                return true;
            }
        }

        if (! ($this->user && $this->user->need('uid') == $booking->need('uid'))) {
            return false;
        }

        if ($booking->need('status') == 'subscription') {
            return false;
        }

        $square = $this->squareManager->get($booking->need('sid'));
        $squareCancelRange = $square->get('range_cancel');

        if (! $squareCancelRange) {
            return false;
        }

        $reservations = $this->reservationManager->getBy(array('bid' => $booking->need('bid')), 'date ASC, time_start ASC');
        $reservation = current($reservations);

        if (! $reservation) {
            return true;
        }

        $reservationStartDate = new DateTime($reservation->need('date') . ' ' . $reservation->need('time_start'));

        $reservationCancelDate = new DateTime();
        $reservationCancelDate->modify('+' . $squareCancelRange . ' sec');

        if ($reservationStartDate > $reservationCancelDate) {
            return true;
        } else {
            return false;
        }
    }

}
