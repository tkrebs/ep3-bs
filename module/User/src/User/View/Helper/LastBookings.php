<?php

namespace User\View\Helper;

use Booking\Manager\BookingManager;
use Booking\Manager\ReservationManager;
use DateTime;
use Square\Manager\SquareManager;
use User\Entity\User;
use Zend\View\Helper\AbstractHelper;

class LastBookings extends AbstractHelper
{

    protected $bookingManager;
    protected $reservationManager;
    protected $squareManager;

    public function __construct(BookingManager $bookingManager, ReservationManager $reservationManager,
        SquareManager $squareManager)
    {
        $this->bookingManager = $bookingManager;
        $this->reservationManager = $reservationManager;
        $this->squareManager = $squareManager;
    }

    public function __invoke(User $user)
    {
        $view = $this->getView();

        $userBookings = $this->bookingManager->getByValidity(array(
            'uid' => $user->need('uid'),
        ));

        if ($userBookings) {
            $this->reservationManager->getByBookings($userBookings);

            $now = new DateTime();

            $lowerLimit = clone $now;
            $lowerLimit->modify('-2 days');

            $upperLimit = clone $now;
            $upperLimit->modify('+28 days');

            $html = '';

            $html .= '<ul style=\'padding: 0px 16px 0px 28px;\'>';

            $bookingsActuallyDisplayed = 0;

            foreach ($userBookings as $booking) {
                $reservations = $booking->needExtra('reservations');

                $bookingDateTimeStart = null;
                $bookingDateTimeEnd = null;

                foreach ($reservations as $reservation) {
                    $tmpDateTimeStart = new DateTime($reservation->need('date') . ' ' . $reservation->need('time_start'));
                    $tmpDateTimeEnd = new DateTime($reservation->need('date') . ' ' . $reservation->need('time_end'));

                    if (is_null($bookingDateTimeStart) || $tmpDateTimeStart < $bookingDateTimeStart) {
                        $bookingDateTimeStart = $tmpDateTimeStart;
                    }

                    if (is_null($bookingDateTimeEnd) || $tmpDateTimeEnd < $bookingDateTimeStart) {
                        $bookingDateTimeEnd = $tmpDateTimeEnd;
                    }
                }

                if ($bookingDateTimeEnd >= $lowerLimit && $bookingDateTimeStart <= $upperLimit) {
                    $square = $this->squareManager->get($booking->need('sid'));
                    $squareType = $view->option('subject.square.type');

                    if ($bookingDateTimeStart < $now) {
                        $html .= sprintf('<li class=\'gray\'><s>%s %s &nbsp; %s</s></li>',
                            $squareType, $view->t($square->need('name')), $view->prettyDate($bookingDateTimeStart));
                    } else {
                        $html .= sprintf('<li><span class=\'my-highlight\'>%s %s</span> &nbsp; %s</li>',
                            $squareType, $view->t($square->need('name')), $view->prettyDate($bookingDateTimeStart));
                    }

                    $bookingsActuallyDisplayed++;
                }
            }

            $html .= '</ul>';

            if (! $bookingsActuallyDisplayed) {
                $html = '<div><em>' . $view->t('You have no imminent bookings.') . '</em></div>';
            }

            return $html;
        } else {
            return '<div><em>' . sprintf($view->t('You have not booked any %s yet.'), $view->option('subject.square.type.plural')) . '</em></div>';
        }
    }

}