<?php

namespace Backend\Controller\Plugin\Booking;

use Booking\Entity\Booking;
use Booking\Manager\BookingManager;
use Booking\Manager\ReservationManager;
use Square\Entity\Square;
use Square\Manager\SquareManager;
use User\Entity\User;
use User\Manager\UserManager;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Create extends AbstractPlugin
{

    protected $bookingManager;
    protected $reservationManager;
    protected $squareManager;
    protected $userManager;
    protected $connection;

    public function __construct(BookingManager $bookingManager, ReservationManager $reservationManager,
        SquareManager $squareManager, UserManager $userManager, ConnectionInterface $connection)
    {
        $this->bookingManager = $bookingManager;
        $this->reservationManager = $reservationManager;
        $this->squareManager = $squareManager;
        $this->userManager = $userManager;
        $this->connection = $connection;
    }

    public function __invoke($user, $timeStart, $timeEnd, $dateStart, $dateEnd, $repeat, $square, $statusBilling, $quantity, $notes = null, $creator = null)
    {
        $controller = $this->getController();

        if (! $this->connection->inTransaction()) {
            $this->connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            /* Determine or create user */

            if (preg_match('/\(([0-9]+)\)/', $user, $matches)) {
                $user = $this->userManager->get($matches[1]);
            } else {
                $users = $this->userManager->getBy(['email' => $user]);

                if ($users) {
                    $user = current($users);
                } else {
                    $users = $this->userManager->getBy(['alias' => $user]);

                    if ($users) {
                        $user = current($users);
                    }
                }
            }

            if (! ($user instanceof User)) {
                $user = $this->userManager->create($user);
            }

            /* Determine square */

            if ($square instanceof Square) {
                $square = $this->squareManager->get($square->get('sid'));
            } else {
                $square = $this->squareManager->get($square);
            }

            /* Determine status */

            $repeat = intval($repeat);

            if ($repeat == 0) {
                $status = 'single';

                $controller->authorize('admin.booking, calendar.create-single-bookings');
            } else {
                $status = 'subscription';

                $controller->authorize('admin.booking, calendar.create-subscription-bookings');
            }

            /* Determine visibility */

            $visibility = 'public';

            /* Determine date */

            $dateStart = new \DateTime($dateStart);
            $dateEnd = new \DateTime($dateEnd);

            /* Determine booking meta */

            $bookingMeta = array();

            if ($status == 'subscription') {
                $bookingMeta['date_start'] = $dateStart->format('Y-m-d');
                $bookingMeta['date_end'] = $dateEnd->format('Y-m-d');
                $bookingMeta['time_start'] = $timeStart;
                $bookingMeta['time_end'] = $timeEnd;
                $bookingMeta['repeat'] = $repeat;
            }

            $bookingMeta['notes'] = $notes;
            $bookingMeta['creator'] = $creator;

            /* Create booking */

            $booking = new Booking(array(
                'uid' => $user->need('uid'),
                'sid' => $square->need('sid'),
                'status' => $status,
                'status_billing' => $statusBilling,
                'visibility' => $visibility,
                'quantity' => $quantity,
            ), $bookingMeta);

            $this->bookingManager->save($booking);

            /* Determine reservations */

            if ($status == 'single') {
                $reservations = $this->reservationManager->create($booking, $dateStart, $timeStart, $timeEnd);
            } else {
                $reservations = $this->reservationManager->createByRange($booking, $dateStart, $dateEnd, $timeStart, $timeEnd, $repeat);
            }

            $booking->setExtra('reservations', $reservations);

            if ($transaction) {
                $this->connection->commit();
                $transaction = false;
            }

            return $booking;

        } catch (\Exception $e) {
            if ($transaction) {
                $this->connection->rollback();
            }

            throw $e;
        }
    }

}
