<?php

namespace Booking\Manager;

use Base\Manager\AbstractManager;
use Booking\Entity\Booking;
use Booking\Entity\Reservation;
use Booking\Entity\ReservationFactory;
use Booking\Table\ReservationMetaTable;
use Booking\Table\ReservationTable;
use DateTime;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Square\Manager\SquareManager;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Where;

class ReservationManager extends AbstractManager
{

    protected $reservationTable;
    protected $reservationMetaTable;
    protected $squareManager;

    /**
     * Creates a new reservation manager object.
     *
     * @param ReservationTable $reservationTable
     * @param ReservationMetaTable $reservationMetaTable
     */
    public function __construct(ReservationTable $reservationTable, ReservationMetaTable $reservationMetaTable,
     SquareManager $squareManager)
    {
        $this->reservationTable = $reservationTable;
        $this->reservationMetaTable = $reservationMetaTable;
        $this->squareManager = $squareManager;
    }

    /**
     * Creates a new reservation.
     *
     * @param int|Booking $booking
     * @param string|DateTime $date
     * @param string|DateTime $timeStart
     * @param string|DateTime $timeEnd
     * @param array $meta
     * @return Reservation
     * @throws InvalidArgumentException
     */
    public function create($booking, $date, $timeStart, $timeEnd, array $meta = array())
    {
        if ($booking instanceof Booking) {
            $booking = $booking->need('bid');
        }

        if (! (is_numeric($booking) && $booking > 0)) {
            throw new InvalidArgumentException('Booking id must be numeric');
        }

        if ($date instanceof DateTime) {
            $date = $date->format('Y-m-d');
        }

        if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
            throw new InvalidArgumentException('Invalid date passed for reservation creation');
        }

        if ($timeStart instanceof DateTime) {
            $timeStart = $timeStart->format('H:i');
        }

        if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeStart)) {
            throw new InvalidArgumentException('Invalid start time passed for reservation creation');
        }

        if ($timeEnd instanceof DateTime) {
            $timeEnd = $timeEnd->format('H:i');
        }

        if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeEnd)) {
            throw new InvalidArgumentException('Invalid end time passed for reservation creation');
        }

        $reservation = new Reservation(array(
            'bid' => $booking,
            'date' => $date,
            'time_start' => $timeStart,
            'time_end' => $timeEnd,
        ), $meta);

        $this->save($reservation);

        $this->getEventManager()->trigger('create', $reservation);

        return $reservation;
    }

    /**
     * Creates an array of new reservations with the same time interval for each date.
     *
     * @param int|Booking $booking
     * @param string|DateTime $dateStart
     * @param string|DateTime $dateEnd
     * @param string|DateTime $timeStart
     * @param string|DateTime $timeEnd
     * @param int $repeat
     * @return array
     * @throws InvalidArgumentException
     */
    public function createByRange($booking, $dateStart, $dateEnd, $timeStart, $timeEnd, $repeat = 1)
    {
        $connection = $this->reservationTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            if (is_string($dateStart)) {
                if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $dateStart)) {
                    throw new InvalidArgumentException('Invalid start date passed for reservation creation');
                }

                $dateStart = new DateTime($dateStart);
            }

            if (! ($dateStart instanceof DateTime)) {
                throw new InvalidArgumentException('Invalid start date type passed for reservation creation');
            }

            if (is_string($dateEnd)) {
                if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $dateEnd)) {
                    throw new InvalidArgumentException('Invalid end date passed for reservation creation');
                }

                $dateEnd = new DateTime($dateEnd);
            }

            if (! ($dateEnd instanceof DateTime)) {
                throw new InvalidArgumentException('Invalid end date type passed for reservation creation');
            }

            if ($dateStart >= $dateEnd) {
                throw new InvalidArgumentException('Invalid date range passed for reservation creation');
            }

            $reservations = array();

            $walkingDate = clone $dateStart;
            $walkingDate->setTime(0, 0, 0);
            $walkingDateLimit = clone $dateEnd;
            $walkingDateLimit->setTime(0, 0, 0);

            while ($walkingDate <= $walkingDateLimit) {
                $reservation = $this->create($booking, $walkingDate, $timeStart, $timeEnd);
                $reservations[$reservation->need('rid')] = $reservation;

                $walkingDate->modify('+' . $repeat. ' day');
            }

            if ($transaction) {
                $connection->commit();
                $transaction = false;
            }

            $this->getEventManager()->trigger('createByRange', $reservations);

            return $reservations;

        } catch (Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
    }

    /**
     * Creates an array of new reservations for the specified datetime interval.
     *
     * @param Booking $booking
     * @param string|DateTime $dateTimeStart
     * @param string|DateTime $dateTimeEnd
     * @return array
     * @throws InvalidArgumentException
     */
    public function createInRange(Booking $booking, $dateTimeStart, $dateTimeEnd)
    {
        $connection = $this->reservationTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            if (is_string($dateTimeStart)) {
                $dateTimeStart = new DateTime($dateTimeStart);
            }

            if (! ($dateTimeStart instanceof DateTime)) {
                throw new InvalidArgumentException('Invalid start datetime passed');
            }

            if (is_string($dateTimeEnd)) {
                $dateTimeEnd = new DateTime($dateTimeEnd);
            }

            if (! ($dateTimeEnd instanceof DateTime)) {
                throw new InvalidArgumentException('Invalid end datetime passed');
            }

            if ($dateTimeStart > $dateTimeEnd) {
                throw new InvalidArgumentException('Invalid datetime range passed');
            }

            $days = $dateTimeEnd->format('z') - $dateTimeStart->format('z');

            if ($days > 186) {
                throw new InvalidArgumentException('Maximum date range exceeded');
            }

            $square = $this->squareManager->get($booking->need('sid'));

            $reservations = array();

            $walkingDate = clone $dateTimeStart;
            $walkingDate->setTime(0, 0, 0);
            $walkingDateLimit = clone $dateTimeEnd;
            $walkingDateLimit->setTime(0, 0, 0);
            $walkingDateIndex = 0;

            while ($walkingDate <= $walkingDateLimit) {
                if ($walkingDateIndex == 0) {
                    $walkingTimeStart = $dateTimeStart->format('H:i');
                } else {
                    $walkingTimeStart = $square->need('time_start');
                }

                if ($walkingDateIndex == $days) {
                    $walkingTimeEnd = $dateTimeEnd->format('H:i');
                } else {
                    $walkingTimeEnd = $square->need('time_end');
                }

                $reservation = $this->create($booking, $walkingDate, $walkingTimeStart, $walkingTimeEnd);
                $reservations[$reservation->need('rid')] = $reservation;

                $walkingDate->modify('+1 day');
                $walkingDateIndex++;
            }

            if ($transaction) {
                $connection->commit();
                $transaction = false;
            }

            $this->getEventManager()->trigger('createRange', $reservations);

            return $reservations;

        } catch (Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
    }

    /**
     * Saves (updates or creates) a reservation.
     *
     * @param Reservation $reservation
     * @return Reservation
     * @throws RuntimeException
     */
    public function save(Reservation $reservation)
    {
        $connection = $this->reservationTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            if ($reservation->get('rid')) {

                /* Update existing reservation */

                /* Determine updated properties */

                $updates = array();

                foreach ($reservation->need('updatedProperties') as $property) {
                    $updates[$property] = $reservation->get($property);
                }

                if ($updates) {
                    $this->reservationTable->update($updates, array('rid' => $reservation->get('rid')));
                }

                /* Determine new meta properties */

                foreach ($reservation->need('insertedMetaProperties') as $metaProperty) {
                    $this->reservationMetaTable->insert(array(
                        'rid' => $reservation->get('rid'),
                        'key' => $metaProperty,
                        'value' => $reservation->needMeta($metaProperty),
                    ));
                }

                /* Determine updated meta properties */

                foreach ($reservation->need('updatedMetaProperties') as $metaProperty) {
                    $this->reservationMetaTable->update(array(
                        'value' => $reservation->needMeta($metaProperty),
                    ), array('rid' => $reservation->get('rid'), 'key' => $metaProperty));
                }

                /* Determine removed meta properties */

                foreach ($reservation->need('removedMetaProperties') as $metaProperty) {
                    $this->reservationMetaTable->delete(array('rid' => $reservation->get('rid'), 'key' => $metaProperty));
                }

                $reservation->reset();

                $this->getEventManager()->trigger('save.update', $reservation);

            } else {

                /* Insert reservation */

                if ($reservation->getExtra('nrid')) {
                    $rid = $reservation->getExtra('nrid');
                } else {
                    $rid = null;
                }

                $this->reservationTable->insert(array(
                    'rid' => $rid,
                    'bid' => $reservation->need('bid'),
                    'date' => $reservation->need('date'),
                    'time_start' => $reservation->need('time_start'),
                    'time_end' => $reservation->need('time_end'),
                ));

                $rid = $this->reservationTable->getLastInsertValue();

                if (! (is_numeric($rid) && $rid > 0)) {
                    throw new RuntimeException('Failed to save reservation');
                }

                foreach ($reservation->need('meta') as $key => $value) {
                    $this->reservationMetaTable->insert(array(
                        'rid' => $rid,
                        'key' => $key,
                        'value' => $value,
                    ));

                    if (! $this->reservationMetaTable->getLastInsertValue()) {
                        throw new RuntimeException( sprintf('Failed to save reservation meta key "%s"', $key) );
                    }
                }

                $reservation->add('rid', $rid);

                $this->getEventManager()->trigger('save.insert', $reservation);
            }

            if ($transaction) {
                $connection->commit();
                $transaction = false;
            }

            $this->getEventManager()->trigger('save', $reservation);

            return $reservation;

        } catch (Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
    }

    /**
     * Gets the reservation by primary id.
     *
     * @param int $rid
     * @param boolean $strict
     * @return Reservation
     * @throws RuntimeException
     */
    public function get($rid, $strict = true)
    {
        $reservation = $this->getBy(array('rid' => $rid));

        if (empty($reservation)) {
            if ($strict) {
                throw new RuntimeException('This reservation does not exist');
            }

            return null;
        } else {
            return current($reservation);
        }
    }

    /**
     * Gets all reservations that match the passed conditions.
     *
     * @param mixed $where              Any valid where conditions, but usually an array with key/value pairs.
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getBy($where, $order = null, $limit = null, $offset = null, $loadMeta = true)
    {
        $select = $this->reservationTable->getSql()->select();

        if ($where) {
            $select->where($where);
        }

        if ($order) {
            $select->order($order);
        }

        if ($limit) {
            $select->limit($limit);

            if ($offset) {
                $select->offset($offset);
            }
        }

        $resultSet = $this->reservationTable->selectWith($select);

        $reservations = ReservationFactory::fromResultSet($resultSet);

        if (! ($reservations && $loadMeta)) {
            return $reservations;
        }

        /* Load reservation meta data */

        $rids = array();

        foreach ($reservations as $reservation) {
            $rids[] = $reservation->need('rid');
        }

        reset($reservations);

        $metaSelect = $this->reservationMetaTable->getSql()->select();
        $metaSelect->where(new In('rid', $rids));

        $metaResultSet = $this->reservationMetaTable->selectWith($metaSelect);

        return ReservationFactory::fromMetaResultSet($reservations, $metaResultSet);
    }

    /**
     * Gets all reservations within the specified date range and within the same time interval for each date.
     *
     * Reservations are ordered by date and start time.
     *
     * @param string|DateTime $dateStart
     * @param string|DateTime $dateEnd
     * @param string $timeStart
     * @param string $timeEnd
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     * @throws InvalidArgumentException
     */
    public function getByRange($dateStart, $dateEnd, $timeStart = null, $timeEnd = null,
        $limit = null, $offset = null, $loadMeta = true)
    {
        if ($dateStart instanceof DateTime) {
            $dateStart = $dateStart->format('Y-m-d');
        }

        if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $dateStart)) {
            throw new InvalidArgumentException('Invalid start date passed for getting reservations by range');
        }

        if ($dateEnd instanceof DateTime) {
            $dateEnd = $dateEnd->format('Y-m-d');
        }

        if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $dateEnd)) {
            throw new InvalidArgumentException('Invalid end date passed for getting reservations by range');
        }

        $where = array('date >= "' . $dateStart . '"', 'date <= "' . $dateEnd . '"');

        if ($timeStart && preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeStart)) {
            $where[] = 'time_start < "' . $timeEnd . '"';
        }

        if ($timeEnd && preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeEnd)) {
            $where[] = 'time_end > "' . $timeStart . '"';
        }

        return $this->getBy($where, 'date, time_start ASC', $limit, $offset, $loadMeta);
    }

    /**
     * Gets all reservations within the specified datetime interval.
     *
     * Reservations are ordered by date and start time.
     *
     * @param DateTime $dateTimeStart
     * @param DateTime $dateTimeEnd
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getInRange(DateTime $dateTimeStart, DateTime $dateTimeEnd,
        $limit = null, $offset = null, $loadMeta = true)
    {
        if ($dateTimeStart->format('Y-m-d') == $dateTimeEnd->format('Y-m-d')) {
            return $this->getByRange($dateTimeStart, $dateTimeEnd, $dateTimeStart->format('H:i'), $dateTimeEnd->format('H:i'),
                $limit, $offset, $loadMeta);
        }

        $where = new Where();

        $nested = $where->nest();
        $nested->equalTo('date', $dateTimeStart->format('Y-m-d'));
        $nested->greaterThan('time_end', $dateTimeStart->format('H:i'));
        $nested->unnest();

        $where->or;

        $nested = $where->nest();
        $nested->greaterThan('date', $dateTimeStart->format('Y-m-d'));
        $nested->lessThan('date', $dateTimeEnd->format('Y-m-d'));
        $nested->unnest();

        $where->or;

        $nested = $where->nest();
        $nested->equalTo('date', $dateTimeEnd->format('Y-m-d'));
        $nested->lessThan('time_start', $dateTimeEnd->format('H:i'));
        $nested->unnest();

        return $this->getBy($where, 'date, time_start ASC', $limit, $offset, $loadMeta);
    }

    /**
     * Gets reservations by bookings.
     *
     * Reservations will be added to the bookings under the extra key 'reservations'.
     * If the bookings array is passed by reference, it will be resorted according to the reservations order.
     *
     * @param array $bookings
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     * @throws InvalidArgumentException
     */
    public function getByBookings(array &$bookings, $order = 'date ASC, time_start ASC',
        $limit = null, $offset = null, $loadMeta = true)
    {
        if (empty($bookings)) {
            return array();
        }

        $bids = array();

        foreach ($bookings as $booking) {
            if (! ($booking instanceof Booking)) {
                throw new InvalidArgumentException('Booking objects required to load from');
            }

            $bid = $booking->need('bid');

            if (! in_array($bid, $bids)) {
                $bids[] = $bid;
            }
        }

        $reservations = $this->getBy(new In('bid', $bids), $order, $limit, $offset, $loadMeta);

        $sortedBookings = array();

        foreach ($reservations as $reservation) {
            $booking = $bookings[$reservation->need('bid')];
            $bookingReservations = $booking->getExtra('reservations');

            if (! is_array($bookingReservations)) {
                $bookingReservations = array();
            }

            $bookingReservations[$reservation->need('rid')] = $reservation;
            $booking->setExtra('reservations', $bookingReservations);

            $sortedBookings[$booking->need('bid')] = $booking;

            $reservation->setExtra('booking', $booking);
        }

        $bookings = $sortedBookings;

        return $reservations;
    }

    /**
     * Calculates the passed seconds per day for each reservation and saves the result
     * as extra key 'time_start_sec' and 'time_end_sec'.
     *
     * @param array $reservations
     * @return array
     */
    public function getSecondsPerDay(array $reservations)
    {
        foreach ($reservations as $reservation) {
            $timeStartParts = explode(':', $reservation->need('time_start'));
            $timeStartSec = $timeStartParts[0] * 3600 + $timeStartParts[1] * 60;
            $reservation->setExtra('time_start_sec', $timeStartSec);

            $timeEndParts = explode(':', $reservation->need('time_end'));
            $timeEndSec = $timeEndParts[0] * 3600 + $timeEndParts[1] * 60;

            if ($timeEndSec == 0) {
                $timeEndSec = 86400;
            }

            $reservation->setExtra('time_end_sec', $timeEndSec);
        }

        return $reservations;
    }

    /**
     * Gets all reservations.
     *
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getAll($order = null, $limit = null, $offset = null, $loadMeta = true)
    {
        return $this->getBy(null, $order, $limit, $offset, $loadMeta);
    }

    /**
     * Deletes one reservation and all respective meta properties (through database foreign keys).
     *
     * @param int|Reservation $reservation
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($reservation)
    {
        if ($reservation instanceof Reservation) {
            $rid = $reservation->need('rid');
        } else {
            $rid = $reservation;
        }

        if (! (is_numeric($rid) && $rid > 0)) {
            throw new InvalidArgumentException('Reservation id must be numeric');
        }

        $reservation = $this->get($rid);

        $deletion = $this->reservationTable->delete(array('rid' => $rid));

        $this->getEventManager()->trigger('delete', $reservation);

        return $deletion;
    }

}
