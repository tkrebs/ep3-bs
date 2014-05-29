<?php

namespace Booking\Manager\Booking;

use Base\Manager\AbstractManager;
use Booking\Entity\Booking;
use Booking\Entity\Booking\Bill;
use Booking\Entity\Booking\BillFactory;
use Booking\Table\Booking\BillTable;
use InvalidArgumentException;
use RuntimeException;
use Zend\Db\Sql\Predicate\In;

class BillManager extends AbstractManager
{

    protected $billTable;

    /**
     * Creates a new booking bill manager object.
     *
     * @param BillTable $billTable
     */
    public function __construct(BillTable $billTable)
    {
        $this->billTable = $billTable;
    }

    /**
     * Saves (updates or creates) a bill.
     *
     * @param Bill $bill
     * @return Bill
     * @throws RuntimeException
     */
    public function save(Bill $bill)
    {
        if ($bill->get('bbid')) {

            /* Update existing bill */

            /* Determine updated properties */

            $updates = array();

            foreach ($bill->need('updatedProperties') as $property) {
                $updates[$property] = $bill->get($property);
            }

            if ($updates) {
                $this->billTable->update($updates, array('bbid' => $bill->get('bbid')));
            }

            $bill->reset();

            $this->getEventManager()->trigger('save.update', $bill);

        } else {

            /* Insert bill */

            if ($bill->getExtra('nbbid')) {
                $bbid = $bill->getExtra('nbbid');
            } else {
                $bbid = null;
            }

            $this->billTable->insert(array(
                'bbid' => $bbid,
                'bid' => $bill->need('bid'),
                'description' => $bill->need('description'),
                'quantity' => $bill->get('quantity'),
                'time' => $bill->get('time'),
                'price' => $bill->need('price'),
                'rate' => $bill->need('rate'),
                'gross' => $bill->need('gross'),
            ));

            $bbid = $this->billTable->getLastInsertValue();

            if (! (is_numeric($bbid) && $bbid > 0)) {
                throw new RuntimeException('Failed to save bill');
            }

            $bill->add('bbid', $bbid);

            $this->getEventManager()->trigger('save.insert', $bill);
        }

        $this->getEventManager()->trigger('save', $bill);

        return $bill;
    }

    /**
     * Gets the bill by primary id.
     *
     * @param int $bbid
     * @param boolean $strict
     * @return Bill
     * @throws RuntimeException
     */
    public function get($bbid, $strict = true)
    {
        $bill = $this->getBy(array('bbid' => $bbid));

        if (empty($bill)) {
            if ($strict) {
                throw new RuntimeException('This bill does not exist');
            }

            return null;
        } else {
            return current($bill);
        }
    }

    /**
     * Gets all bills that match the passed conditions.
     *
     * @param mixed $where              Any valid where conditions, but usually an array with key/value pairs.
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getBy($where, $order = null, $limit = null, $offset = null)
    {
        $select = $this->billTable->getSql()->select();

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

        $resultSet = $this->billTable->selectWith($select);

        return BillFactory::fromResultSet($resultSet);
    }

    /**
     * Gets bills by bookings.
     *
     * Bills will be added to the bookings under the extra key 'bills'.
     *
     * @param array $bookings
     * @return array
     * @throws InvalidArgumentException
     */
    public function getByBookings(array $bookings)
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

        $bills = $this->getBy(new In(BillTable::NAME . '.bid', $bids));

        foreach ($bills as $bill) {
            $booking = $bookings[$bill->need('bid')];
            $bookingBills = $booking->getExtra('bills');

            if (! is_array($bookingBills)) {
                $bookingBills = array();
            }

            $bookingBills[$bill->need('bbid')] = $bill;
            $booking->setExtra('bills', $bookingBills);
        }

        return $bills;
    }

    /**
     * Gets all bills.
     *
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($order = null, $limit = null, $offset = null)
    {
        return $this->getBy(null, $order, $limit, $offset);
    }

    /**
     * Deletes one bill.
     *
     * @param int|Bill $bill
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($bill)
    {
        if ($bill instanceof Bill) {
            $bbid = $bill->need('bbid');
        } else {
            $bbid = $bill;
        }

        if (! (is_numeric($bbid) && $bbid > 0)) {
            throw new InvalidArgumentException('Bill id must be numeric');
        }

        $bill = $this->get($bbid);

        $deletion = $this->billTable->delete(array('bbid' => $bbid));

        $this->getEventManager()->trigger('delete', $bill);

        return $deletion;
    }

}