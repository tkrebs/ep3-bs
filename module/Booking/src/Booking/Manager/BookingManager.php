<?php

namespace Booking\Manager;

use Base\Manager\AbstractManager;
use Booking\Entity\Booking;
use Booking\Entity\BookingFactory;
use Booking\Entity\Reservation;
use Booking\Table\BookingMetaTable;
use Booking\Table\BookingTable;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Zend\Db\Sql\Predicate\In;

class BookingManager extends AbstractManager
{

    protected $bookingTable;
    protected $bookingMetaTable;

    /**
     * Creates a new booking manager object.
     *
     * @param BookingTable $bookingTable
     * @param BookingMetaTable $bookingMetaTable
     */
    public function __construct(BookingTable $bookingTable, BookingMetaTable $bookingMetaTable)
    {
        $this->bookingTable = $bookingTable;
        $this->bookingMetaTable = $bookingMetaTable;
    }

    /**
     * Saves (updates or creates) a booking.
     *
     * @param Booking $booking
     * @return Booking
     * @throws RuntimeException
     */
    public function save(Booking $booking)
    {
        $connection = $this->bookingTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            if ($booking->get('bid')) {

                /* Update existing booking */

                /* Determine updated properties */

                $updates = array();

                foreach ($booking->need('updatedProperties') as $property) {
                    $updates[$property] = $booking->get($property);
                }

                if ($updates) {
                    $this->bookingTable->update($updates, array('bid' => $booking->get('bid')));
                }

                /* Determine new meta properties */

                foreach ($booking->need('insertedMetaProperties') as $metaProperty) {
                    $this->bookingMetaTable->insert(array(
                        'bid' => $booking->get('bid'),
                        'key' => $metaProperty,
                        'value' => $booking->needMeta($metaProperty),
                    ));
                }

                /* Determine updated meta properties */

                foreach ($booking->need('updatedMetaProperties') as $metaProperty) {
                    $this->bookingMetaTable->update(array(
                        'value' => $booking->needMeta($metaProperty),
                    ), array('bid' => $booking->get('bid'), 'key' => $metaProperty));
                }

                /* Determine removed meta properties */

                foreach ($booking->need('removedMetaProperties') as $metaProperty) {
                    $this->bookingMetaTable->delete(array('bid' => $booking->get('bid'), 'key' => $metaProperty));
                }

                $booking->reset();

                $this->getEventManager()->trigger('save.update', $booking);

            } else {

                /* Insert booking */

                $created = date('Y-m-d H:i:s');

                if ($booking->getExtra('nbid')) {
                    $bid = $booking->getExtra('nbid');
                } else {
                    $bid = null;
                }

                $this->bookingTable->insert(array(
                    'bid' => $bid,
                    'uid' => $booking->need('uid'),
                    'sid' => $booking->need('sid'),
                    'status' => $booking->need('status'),
                    'status_billing' => $booking->need('status_billing'),
                    'visibility' => $booking->need('visibility'),
                    'quantity' => $booking->need('quantity'),
                    'created' => $booking->get('created', $created),
                ));

                $bid = $this->bookingTable->getLastInsertValue();

                if (! (is_numeric($bid) && $bid > 0)) {
                    throw new RuntimeException('Failed to save booking');
                }

                foreach ($booking->need('meta') as $key => $value) {
                    $this->bookingMetaTable->insert(array(
                        'bid' => $bid,
                        'key' => $key,
                        'value' => $value,
                    ));

                    if (! $this->bookingMetaTable->getLastInsertValue()) {
                        throw new RuntimeException( sprintf('Failed to save booking meta key "%s"', $key) );
                    }
                }

                $booking->add('bid', $bid);

                if (! $booking->get('created')) {
                    $booking->add('created', $created);
                }

                $this->getEventManager()->trigger('save.insert', $booking);
            }

            if ($transaction) {
                $connection->commit();
                $transaction = false;
            }

            $this->getEventManager()->trigger('save', $booking);

            return $booking;

        } catch (Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
    }

    /**
     * Gets the booking by primary id.
     *
     * @param int $bid
     * @param boolean $strict
     * @return Booking
     * @throws RuntimeException
     */
    public function get($bid, $strict = true)
    {
        $booking = $this->getBy(array('bid' => $bid));

        if (empty($booking)) {
            if ($strict) {
                throw new RuntimeException('This booking does not exist');
            }

            return null;
        } else {
            return current($booking);
        }
    }

    /**
     * Gets all bookings that match the passed conditions.
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
        $select = $this->bookingTable->getSql()->select();

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

        $resultSet = $this->bookingTable->selectWith($select);

        $bookings = BookingFactory::fromResultSet($resultSet);

        if (! ($bookings && $loadMeta)) {
            return $bookings;
        }

        /* Load booking meta data */

        $bids = array();

        foreach ($bookings as $booking) {
            $bids[] = $booking->need('bid');
        }

        reset($bookings);

        $metaSelect = $this->bookingMetaTable->getSql()->select();
        $metaSelect->where(new In('bid', $bids));

        $metaResultSet = $this->bookingMetaTable->selectWith($metaSelect);

        return BookingFactory::fromMetaResultSet($bookings, $metaResultSet);
    }

    /**
     * Gets bookings by reservations.
     *
     * Bookings will be added to the reservations under the extra key 'booking'.
     * Reservations will be added to the bookings under the extra key 'reservations'.
     *
     * Quick and dirty warning: If $where is passed, $reservations are referenced-filtered.
     *
     * @param array $reservations
     * @param array $where
     * @return array
     * @throws InvalidArgumentException
     */
    public function getByReservations(array &$reservations, array $where = array())
    {
        if (empty($reservations)) {
            return array();
        }

        $bids = array();

        foreach ($reservations as $reservation) {
            if (! ($reservation instanceof Reservation)) {
                throw new InvalidArgumentException('Reservation objects required to load from');
            }

            $bid = $reservation->need('bid');

            if (! in_array($bid, $bids)) {
                $bids[] = $bid;
            }
        }

        $bookings = $this->getBy(array_merge(array(new In(BookingTable::NAME . '.bid', $bids)), $where));

        $unsetReservations = array();

        foreach ($reservations as $rid => $reservation) {
            if (isset($bookings[$reservation->need('bid')])) {
                $booking = $bookings[$reservation->need('bid')];
                $bookingReservations = $booking->getExtra('reservations');

                if (! is_array($bookingReservations)) {
                    $bookingReservations = array();
                }

                $bookingReservations[$rid] = $reservation;
                $booking->setExtra('reservations', $bookingReservations);

                $reservation->setExtra('booking', $booking);
            } else {
                if (! in_array($rid, $unsetReservations)) {
                    $unsetReservations[] = $rid;
                }
            }
        }

        foreach ($unsetReservations as $rid) {
            unset($reservations[$rid]);
        }

        return $bookings;
    }

    /**
     * Gets all bookings that match the passed conditions and are considered valid.
     *
     * @param mixed $where              Any valid where conditions, but usually an array with key/value pairs.
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getByValidity($where, $order = null, $limit = null, $offset = null, $loadMeta = true)
    {
        $bookings = $this->getBy($where, $order, $limit, $offset, $loadMeta);

        $validBookings = array();

        foreach ($bookings as $booking) {
            if ($booking->need('status') != 'cancelled') {
                if ($booking->need('visibility') == 'public') {
                    $validBookings[$booking->need('bid')] = $booking;
                }
            }
        }

        return $validBookings;
    }

    /**
     * Gets all bookings.
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
     * Deletes one booking, all respective meta properties and all respective bills (through database foreign keys).
     *
     * @param int|Booking $booking
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($booking)
    {
        if ($booking instanceof Booking) {
            $bid = $booking->need('bid');
        } else {
            $bid = $booking;
        }

        if (! (is_numeric($bid) && $bid > 0)) {
            throw new InvalidArgumentException('Booking id must be numeric');
        }

        $booking = $this->get($bid);

        $deletion = $this->bookingTable->delete(array('bid' => $bid));

        $this->getEventManager()->trigger('delete', $booking);

        return $deletion;
    }

}
