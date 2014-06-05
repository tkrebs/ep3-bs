<?php

namespace Backend\Controller\Plugin\Booking;

use Booking\Entity\Booking;
use Booking\Manager\BookingManager;
use Booking\Manager\ReservationManager;
use Square\Entity\Square;
use Square\Manager\SquareManager;
use User\Manager\UserManager;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Update extends AbstractPlugin
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

    public function __invoke($rid, $newUser, $newTimeStart, $newTimeEnd, $newDate, $newSquare,
        $newStatusBilling, $newQuantity, $newNotes = null)
    {
        $controller = $this->getController();
        $controller->authorize('admin.booking');

        if (! $this->connection->inTransaction()) {
            $this->connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            $reservation = $this->reservationManager->get($rid);
            $booking = $this->bookingManager->get($reservation->get('bid'));

            /* Determine or create user */

            if (preg_match('/\(([0-9]+)\)/', $newUser, $matches)) {
                $newUser = $matches[1];
            }

            $users = $this->userManager->interpret($newUser, 2);

            if (count($users) == 1) {
                $user = current($users);
            } else {
                $user = $this->userManager->create($newUser);
            }

            /* Determine date */

            $newDate = new \DateTime($newDate);

            /* Determine square */

            if ($newSquare instanceof Square) {
                $square = $this->squareManager->get($newSquare->get('sid'));
            } else {
                $square = $this->squareManager->get($newSquare);
            }

            /* Update booking */

            $booking->set('uid', $user->need('uid'));
            $booking->set('sid', $square->need('sid'));
            $booking->set('status_billing', $newStatusBilling);
            $booking->set('quantity', $newQuantity);
            $booking->setMeta('notes', $newNotes);

            $this->bookingManager->save($booking);

            /* Update reservation */

            $reservation->set('time_start', $newTimeStart);
            $reservation->set('time_end', $newTimeEnd);
            $reservation->set('date', $newDate->format('Y-m-d'));

            $this->reservationManager->save($reservation);

            if ($transaction) {
                $this->connection->commit();
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