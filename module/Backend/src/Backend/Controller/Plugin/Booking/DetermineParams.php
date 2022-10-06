<?php

namespace Backend\Controller\Plugin\Booking;

use Booking\Entity\Booking;
use Booking\Manager\BookingManager;
use Booking\Manager\ReservationManager;
use Square\Manager\SquareManager;
use User\Manager\UserManager;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DetermineParams extends AbstractPlugin
{

    protected $bookingManager;
    protected $reservationManager;
    protected $squareManager;
    protected $userManager;

    public function __construct(BookingManager $bookingManager, ReservationManager $reservationManager,
        SquareManager $squareManager, UserManager $userManager)
    {
        $this->bookingManager = $bookingManager;
        $this->reservationManager = $reservationManager;
        $this->squareManager = $squareManager;
        $this->userManager = $userManager;
    }

    public function __invoke($allowCancelled = false)
    {
        $controller = $this->getController();

        $dateStartParam = $controller->params()->fromQuery('ds');
        $dateEndParam = $controller->params()->fromQuery('de');
        $timeStartParam = $controller->params()->fromQuery('ts');
        $timeEndParam = $controller->params()->fromQuery('te');
        $squareParam = $controller->params()->fromQuery('s');

        /* Determine dates (or set to default) */

        if ($dateStartParam) {
            $dateTimeStart = new \DateTime($dateStartParam);
        } else {
            $dateTimeStart = new \DateTime();
        }

        if (! $dateStartParam) {
            $dateTimeStart->modify('+1 month');
        }


        if ($dateEndParam) {
            $dateTimeEnd = new \DateTime($dateEndParam);
        } else {
            $dateTimeEnd = clone $dateTimeStart;
        }

        /* Determine times (or set to default) */

        if ($timeStartParam && preg_match('/^[0-9]?[0-9]:[0-9][0-9]$/', $timeStartParam)) {
            $timeStartParamParts = explode(':', $timeStartParam);
        } else {
            $timeStartParamParts = array(10, 0);
        }

        $dateTimeStart->setTime($timeStartParamParts[0], $timeStartParamParts[1]);

        if ($timeEndParam && preg_match('/^[0-9]?[0-9]:[0-9][0-9]$/', $timeEndParam)) {
            $timeEndParamParts = explode(':', $timeEndParam);
        } else {
            $timeEndParamParts = array(12, 0);
        }

        $dateTimeEnd->setTime($timeEndParamParts[0], $timeEndParamParts[1]);

        /* Determine square (or set to first one) */

        if ($squareParam) {
            $square = $this->squareManager->get($squareParam);
        } else {
            $squares = $this->squareManager->getAll();
            $square = current($squares);
        }

        /* Determine reservations */

        $reservationParam = $controller->params()->fromQuery('r');

        $reservations = $this->reservationManager->getInRange($dateTimeStart, $dateTimeEnd);

        if ($reservations) {
            if ($reservationParam && $allowCancelled) {
                $bookings = $this->bookingManager->getByReservations($reservations);
            } else {
                $bookings = $this->bookingManager->getByReservations($reservations, array(new Operator('status', '!=', 'cancelled')));
            }

            $this->userManager->getByBookings($bookings);

            /* Filter reservations with correct bookings */

            $validReservations = array();

            foreach ($reservations as $rid => $reservation) {

                /* Filter cancelled bookings */

                if ($reservation->getExtra('booking')) {

                    /* Filter wrong squares */

                    if ($reservation->getExtra('booking')->get('sid') == $square->get('sid')) {
                        $validReservations[$rid] = $reservation;
                    }
                }
            }

            $reservations = $validReservations;

            /* Filter reservations with passed param */

            if ($reservationParam) {
                if (isset($reservations[$reservationParam])) {
                    $reservations = array( $reservations[$reservationParam] );
                } else {
                    throw new \RuntimeException('The requested reservation does not exist (here)');
                }
            }
        }

        /* Determine edit mode */

        $editModeParam = $controller->params()->fromQuery('em');

        if ($editModeParam == 'booking' || $editModeParam == 'reservation') {
            $editMode = $editModeParam;
        } else {
            $editMode = null;
        }

        /* Return gathered params */

        return array(
            'query' => array(
                'ds' => $dateStartParam,
                'de' => $dateEndParam,
                'ts' => $timeStartParam,
                'te' => $timeEndParam,
                's' => $squareParam,
                'r' => $reservationParam,
            ),
            'dateTimeStart' => $dateTimeStart,
            'dateTimeEnd' => $dateTimeEnd,
            'square' => $square,
            'reservations' => $reservations,
            'editMode' => $editMode,
        );
    }

}
