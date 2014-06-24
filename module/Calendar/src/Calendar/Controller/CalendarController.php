<?php

namespace Calendar\Controller;

use DateTime;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;

class CalendarController extends AbstractActionController
{

    public function indexAction()
    {
        $serviceManager = $this->getServiceLocator();

        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $eventManager = $serviceManager->get('Event\Manager\EventManager');
        $reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');

        $daysToRender = $this->option('service.calendar.days', false);

        $dateStart = $this->calendarDetermineDate();
        $dateEnd = clone $dateStart;
        $dateEnd->modify('+' . ($daysToRender - 1) . ' days');
        $dateEnd->setTime(23, 59, 59);
        $dateNow = new DateTime();

        $timeStart = $squareManager->getMinStartTime();
        $timeEnd = $squareManager->getMaxEndTime();
        $timeBlock = $squareManager->getMinTimeBlock();
        $timeBlockCount = ceil(($timeEnd - $timeStart) / $timeBlock);

        $squares = $this->calendarDetermineSquares();
        $squaresCount = count($squares);

        $reservations = $reservationManager->getInRange($dateStart, $dateEnd);
        $bookings = $bookingManager->getByReservations($reservations);
        $events = $eventManager->getInRange($dateStart, $dateEnd);

        $reservationManager->getSecondsPerDay($reservations);
        $eventManager->getSecondsPerDay($events);

        $userSessionManager = $serviceManager->get('User\Manager\UserSessionManager');
        $user = $userSessionManager->getSessionUser();

        if ($user && $user->can('calendar.see-data')) {
            $userManager = $serviceManager->get('User\Manager\UserManager');
            $userManager->getByBookings($bookings);

            $dateNow->setTime(0, 0, 0);
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
            'squares' => $squares,
            'squaresCount' => $squaresCount,
            'reservations' => $reservations,
            'events' => $events,
            'user' => $user,
        );
    }

}