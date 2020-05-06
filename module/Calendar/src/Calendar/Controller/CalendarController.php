<?php

namespace Calendar\Controller;

use DateTime;
use Zend\Mvc\Controller\AbstractActionController;

class CalendarController extends AbstractActionController
{

    public function indexAction()
    {
        $serviceManager = @$this->getServiceLocator();

        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $eventManager = $serviceManager->get('Event\Manager\EventManager');
        $reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');

        $daysToRender = $this->option('service.calendar.days', false);
        $dayExceptions = $this->option('service.calendar.day-exceptions');

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
        } else {
            $dayExceptions = [];
            $dayExceptionsExceptions = [];
        }

        $dateStart = $this->calendarDetermineDate();
        $dateEnd = clone $dateStart;

        for ($i = 1; $i < $daysToRender; $i++) {
            $dateEnd->modify('+1 day');

            if (in_array($dateEnd->format($this->t('Y-m-d')), $dayExceptions) ||
                in_array($this->t($dateEnd->format('l')), $dayExceptions)) {

                if (in_array($dateEnd->format($this->t('Y-m-d')), $dayExceptionsExceptions)) {
                    continue;
                }

                $daysToRender++;

                if ($daysToRender > 24) {
                    throw new \RuntimeException('Too many days are hidden from calendar');
                }
            }
        }

        $dateEnd->setTime(23, 59, 59);
        $dateNow = new DateTime();

        $timeStart = $squareManager->getMinStartTime();
        $timeEnd = $squareManager->getMaxEndTime();
        $timeBlock = $squareManager->getMinTimeBlock();
        $timeBlockCount = ceil(($timeEnd - $timeStart) / $timeBlock);

        $squares = $this->calendarDetermineSquares();
        $squaresCount = count($squares);
        $squaresFilter = $this->params()->fromQuery('squares');

        $reservations = $reservationManager->getInRange($dateStart, $dateEnd);
        $bookings = $bookingManager->getByReservations($reservations);
        $events = $eventManager->getInRange($dateStart, $dateEnd);

        $reservationManager->getSecondsPerDay($reservations);
        $eventManager->getSecondsPerDay($events);

        $userSessionManager = $serviceManager->get('User\Manager\UserSessionManager');
        $user = $userSessionManager->getSessionUser();

        $getBookingUsers = false;

        if ($user && $user->can('calendar.see-data')) {
            $getBookingUsers = true;
            $dateNow->setTime(0, 0, 0);
        }

        if ($user && $squareManager->hasOneWithPrivateNames()) {
            $getBookingUsers = true;
        }

        if ($squareManager->hasOneWithPublicNames()) {
            $getBookingUsers = true;
        }

        if ($getBookingUsers) {
            $userManager = $serviceManager->get('User\Manager\UserManager');
            $userManager->getByBookings($bookings);
        }

        $this->redirectBack()->setOrigin('calendar');

        return array(
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'dateNow' => $dateNow,
            'timeStart' => $timeStart,
            'timeEnd' => $timeEnd,
            'timeBlock' => $timeBlock,
            'timeBlockCount' => $timeBlockCount,
            'daysToRender' => $daysToRender,
            'dayExceptions' => $dayExceptions,
            'dayExceptionsExceptions' => $dayExceptionsExceptions,
            'squares' => $squares,
            'squaresCount' => $squaresCount,
            'squaresFilter' => $squaresFilter,
            'reservations' => $reservations,
            'events' => $events,
            'user' => $user,
        );
    }

}
