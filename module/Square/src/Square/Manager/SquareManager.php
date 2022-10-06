<?php

namespace Square\Manager;

use Base\Manager\AbstractManager;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Square\Entity\Square;
use Square\Entity\SquareFactory;
use Square\Table\SquareMetaTable;
use Square\Table\SquareTable;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Where;

class SquareManager extends AbstractManager
{

    protected $squareTable;
    protected $squareMetaTable;

    protected $squares = array();
    protected $activeSquares = array();

    /**
     * Creates a new square manager object.
     *
     * Preloads all available squares from the database.
     *
     * @param SquareTable $squareTable
     * @param SquareMetaTable $squareMetaTable
     * @param string $locale
     */
    public function __construct(SquareTable $squareTable, SquareMetaTable $squareMetaTable, $locale)
    {
        $this->squareTable = $squareTable;
        $this->squareMetaTable = $squareMetaTable;

        $loadSquares = function () {
            $select = $this->squareTable->getSql()->select();
            $select->order('priority ASC');

            $resultSet = $this->squareTable->selectWith($select);

            $this->squares = SquareFactory::fromResultSet($resultSet);
        };

        $loadSquares();

        /*
         * Patch database structure on the fly
         */
        if ($this->squares) {
            $referenceSquare = current($this->squares);

            if ($referenceSquare->get('allow_notes') === null) {
                $this->squareTable->getAdapter()->query('ALTER TABLE `bs_squares` ADD `allow_notes` tinyint(1) NOT NULL DEFAULT \'0\' AFTER `capacity_heterogenic`;', 'execute');
                $loadSquares();
            }

            if ($referenceSquare->get('min_range_book') === null) {
                $this->squareTable->getAdapter()->query('ALTER TABLE `bs_squares` ADD `min_range_book` INT UNSIGNED NOT NULL DEFAULT \'0\' AFTER `time_block_bookable_max`;', 'execute');
                $loadSquares();
            }

            if ($referenceSquare->get('max_active_bookings') === null) {
                $this->squareTable->getAdapter()->query('ALTER TABLE `bs_squares` ADD `max_active_bookings` INT UNSIGNED NOT NULL DEFAULT \'0\' AFTER `range_book`;', 'execute');
                $loadSquares();
            }
        }

        /* Load square meta data */

        if ($this->squares) {

            $sids = array();

            foreach ($this->squares as $square) {
                $sids[] = $square->need('sid');
            }

            reset($this->squares);

            $metaWhere = new Where();
            $metaWhere->in('sid', $sids);
            $metaWhere->and;
            $metaWhereNested = $metaWhere->nest();
            $metaWhereNested->isNull('locale');
            $metaWhereNested->or;
            $metaWhereNested->equalTo('locale', $locale);
            $metaWhereNested->unnest();

            $metaSelect = $this->squareMetaTable->getSql()->select();
            $metaSelect->where($metaWhere);
            $metaSelect->order('locale ASC');

            $metaResultSet = $this->squareMetaTable->selectWith($metaSelect);

            SquareFactory::fromMetaResultSet($this->squares, $metaResultSet);

            /* Prepare active squares */

            $this->activeSquares = $this->getAllVisible();
        }
    }

    /**
     * Saves (updates or creates) a square.
     *
     * @param Square $square
     * @throws Exception
     * @return Square
     */
    public function save(Square $square)
    {
        $connection = $this->squareTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            if ($square->get('sid')) {

                /* Update existing square */

                /* Determine updated properties */

                $updates = array();

                foreach ($square->need('updatedProperties') as $property) {
                    $updates[$property] = $square->get($property);
                }

                if ($updates) {
                    $this->squareTable->update($updates, array('sid' => $square->get('sid')));
                }

                /* Determine new meta properties */

                foreach ($square->need('insertedMetaProperties') as $metaProperty) {
                    $this->squareMetaTable->insert(array(
                        'sid' => $square->get('sid'),
                        'key' => $metaProperty,
                        'value' => $square->needMeta($metaProperty),
                        'locale' => $square->getMetaLocale($metaProperty),
                    ));
                }

                /* Determine updated meta properties */

                foreach ($square->need('updatedMetaProperties') as $metaProperty) {
                    $locale = $square->getMetaLocale($metaProperty);

                    $where = array('sid' => $square->get('sid'), 'key' => $metaProperty);

                    if (is_null($locale)) {
                        $where[] = new IsNull('locale');
                    } else {
                        $where['locale'] = $locale;
                    }

                    $this->squareMetaTable->update(array(
                        'value' => $square->needMeta($metaProperty),
                    ), $where);
                }

                /* Determine removed meta properties */

                foreach ($square->need('removedMetaProperties') as $metaProperty) {
                    $this->squareMetaTable->delete(array('sid' => $square->get('sid'), 'key' => $metaProperty, 'locale' => $square->getMetaLocale($metaProperty)));
                }

                $square->reset();

                $this->getEventManager()->trigger('save.update', $square);

            } else {

                /* Insert square */

                if ($square->getExtra('nsid')) {
                    $sid = $square->getExtra('nsid');
                } else {
                    $sid = null;
                }

                $this->squareTable->insert(array(
                    'sid' => $sid,
                    'name' => $square->need('name'),
                    'status' => $square->get('status', 'enabled'),
                    'priority' => $square->need('priority'),
                    'capacity' => $square->need('capacity'),
                    'capacity_heterogenic' => $square->need('capacity_heterogenic'),
                    'time_start' => $square->need('time_start'),
                    'time_end' => $square->need('time_end'),
                    'time_block' => $square->need('time_block'),
                    'time_block_bookable' => $square->need('time_block_bookable'),
                    'time_block_bookable_max' => $square->need('time_block_bookable_max'),
                    'min_range_book' => $square->get('min_range_book'),
                    'range_book' => $square->need('range_book'),
                    'max_active_bookings' => $square->get('max_active_bookings'),
                    'range_cancel' => $square->need('range_cancel'),
                ));

                $sid = $this->squareTable->getLastInsertValue();

                if (! (is_numeric($sid) && $sid > 0)) {
                    throw new RuntimeException('Failed to save square');
                }

                foreach ($square->need('meta') as $key => $value) {
                    $this->squareMetaTable->insert(array(
                        'sid' => $sid,
                        'key' => $key,
                        'value' => $value,
                    ));

                    if (! $this->squareMetaTable->getLastInsertValue()) {
                        throw new RuntimeException( sprintf('Failed to save square meta key "%s"', $key) );
                    }
                }

                $square->add('sid', $sid);

                $this->getEventManager()->trigger('save.insert', $square);
            }

            if ($transaction) {
                $connection->commit();
                $transaction = false;
            }

            $this->getEventManager()->trigger('save', $square);

            return $square;

        } catch (Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
    }

    /**
     * Gets the square by primary id.
     *
     * @param int $sid
     * @param boolean $strict
     * @return Square
     * @throws RuntimeException
     */
    public function get($sid, $strict = true)
    {
        if (isset($this->squares[$sid])) {
            return $this->squares[$sid];
        } else {
            if ($strict) {
                throw new RuntimeException('This square does not exist');
            }

            return null;
        }
    }

    /**
     * Gets all squares that match the passed conditions.
     *
     * @param array $where
     * @return array
     */
    public function getBy(array $where)
    {
        $squares = array();

        foreach ($this->squares as $square) {
            foreach ($where as $key => $value) {
                if ($square->need($key) != $value) {
                    continue 2;
                }
            }

            $squares[$square->need('sid')] = $square;
        }

        return $squares;
    }

    /**
     * Gets all squares.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->squares;
    }

    /**
     * Gets all visible squares.
     *
     * @return array
     */
    public function getAllVisible()
    {
        if (! $this->activeSquares) {
            $this->activeSquares = array();

            foreach ($this->squares as $square) {
                if ($square->need('status') == 'enabled' || $square->need('status') == 'readonly') {
                    $this->activeSquares[$square->need('sid')] = $square;
                }
            }
        }

        return $this->activeSquares;
    }

    /**
     * Gets the maximum capacity (considering all squares).
     *
     * @return int
     */
    public function getMaxCapacity()
    {
        $maxCapacity = 0;

        foreach ($this->activeSquares as $square) {
            $capacity = $square->need('capacity');

            if ($capacity > $maxCapacity) {
                $maxCapacity = $capacity;
            }
        }

        return $maxCapacity;
    }

    /**
     * Gets the earliest start time (considering all squares) in seconds per day.
     *
     * Saves each calculated time as extra key 'time_start_sec'.
     *
     * @return int
     */
    public function getMinStartTime()
    {
        $minStartTime = 86400;

        foreach ($this->activeSquares as $square) {
            $currentTimeParts = explode(':', $square->need('time_start'));
            $currentTime = $currentTimeParts[0] * 3600 + $currentTimeParts[1] * 60;

            if ($minStartTime > $currentTime) {
                $minStartTime = $currentTime;
            }

            $square->setExtra('time_start_sec', $currentTime);
        }

        return $minStartTime;
    }

    /**
     * Gets the latest end time (considering all squares) in seconds per day.
     *
     * Saves each calculated time as extra key 'time_end_sec'.
     *
     * @return int
     */
    public function getMaxEndTime()
    {
        $maxEndTime = 0;

        foreach ($this->activeSquares as $square) {
            $currentTimeParts = explode(':', $square->need('time_end'));
            $currentTime = $currentTimeParts[0] * 3600 + $currentTimeParts[1] * 60;

            if ($maxEndTime < $currentTime) {
                $maxEndTime = $currentTime;
            }

            $square->setExtra('time_end_sec', $currentTime);
        }

        return $maxEndTime;
    }

    /**
     * Gets the smallest time block (considering all squares) in seconds.
     *
     * @return int
     */
    public function getMinTimeBlock()
    {
        $minTimeBlock = 86400;

        foreach ($this->activeSquares as $square) {
            if ($minTimeBlock > $square->need('time_block')) {
                $minTimeBlock = $square->need('time_block');
            }
        }

        return $minTimeBlock;
    }

    /**
     * Gets the smallest time block bookable (considering all squares) in seconds.
     *
     * @return int
     */
    public function getMinTimeBlockBookable()
    {
        $minTimeBlockBookable = 86400;

        foreach ($this->activeSquares as $square) {
            if ($minTimeBlockBookable > $square->need('time_block_bookable')) {
                $minTimeBlockBookable = $square->need('time_block_bookable');
            }
        }

        return $minTimeBlockBookable;
    }

    /**
     * Gets the largest time block bookable max (considering all squares) in seconds.
     *
     * @return int
     */
    public function getMaxTimeBlockBookableMax()
    {
        $maxTimeBlockBookableMax = 0;

        foreach ($this->activeSquares as $square) {
            $timeBlockBookableMax = $square->get('time_block_bookable_max');

            if ($timeBlockBookableMax) {
                if ($maxTimeBlockBookableMax < $timeBlockBookableMax) {
                    $maxTimeBlockBookableMax = $timeBlockBookableMax;
                }
            }
        }

        return $maxTimeBlockBookableMax;
    }

    /**
     * Gets the smallest booking range (considering all squares) in seconds.
     *
     * @return int|null
     */
    public function getMinBookRange()
    {
        $minBookRange = null;

        foreach ($this->activeSquares as $square) {
            $bookRange = $square->get('range_book');

            if ($bookRange) {
                if (is_null($minBookRange) || $minBookRange > $bookRange) {
                    $minBookRange = $bookRange;
                }
            }
        }

        return $minBookRange;
    }

    /**
     * Gets the smallest cancel range (considering all squares) in seconds.
     *
     * @return int|null
     */
    public function getMinCancelRange()
    {
        $minCancelRange = null;

        foreach ($this->activeSquares as $square) {
            $cancelRange = $square->get('range_cancel');

            if ($cancelRange) {
                if (is_null($minCancelRange) || $minCancelRange > $cancelRange) {
                    $minCancelRange = $cancelRange;
                }
            }
        }

        return $minCancelRange;
    }

    public function hasOneWithPublicNames()
    {
        $hasOne = false;

        foreach ($this->activeSquares as $square) {
            if ($square->getMeta('public_names', 'false') == 'true') {
                $hasOne = true;
            }
        }

        return $hasOne;
    }

    public function hasOneWithPrivateNames()
    {
        $hasOne = false;

        foreach ($this->activeSquares as $square) {
            if ($square->getMeta('private_names', 'false') == 'true') {
                $hasOne = true;
            }
        }

        return $hasOne;
    }

    /**
     * Deletes one square and all respective meta properties (through database foreign keys).
     *
     * @param int|Square $square
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($square)
    {
        if ($square instanceof Square) {
            $sid = $square->need('sid');
        } else {
            $sid = $square;
        }

        if (! (is_numeric($sid) && $sid > 0)) {
            throw new InvalidArgumentException('Square id must be numeric');
        }

        $square = $this->get($sid);

        $deletion = $this->squareTable->delete(array('sid' => $sid));

        $this->getEventManager()->trigger('delete', $square);

        return $deletion;
    }

}
