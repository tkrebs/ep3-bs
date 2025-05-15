<?php

namespace Square\Manager;

use Base\Manager\AbstractManager;
use DateTime;
use InvalidArgumentException;
use RuntimeException;
use Square\Entity\Square;
use Square\Table\SquarePricingTable;
use Zend\Db\Adapter\Adapter;

class SquarePricingManager extends AbstractManager
{

    protected $squarePricingTable;
    protected $squareManager;

    protected $rules = array();

    /**
     * Creates a new square pricing manager object.
     *
     * Preloads all available pricing rules from the database.
     *
     * @param SquarePricingTable $squarePricingTable
     * @param SquareManager $squareManager
     */
    public function __construct(SquarePricingTable $squarePricingTable, SquareManager $squareManager)
    {
        $this->squarePricingTable = $squarePricingTable;
        $this->squareManager = $squareManager;

        $select = $squarePricingTable->getSql()->select();
        $select->order('priority ASC');

        foreach ($squarePricingTable->selectWith($select) as $result) {
            $this->rules[] = $result;
        }
    }

    /**
     * Creates a new pricing rule set.
     *
     * This will always truncate the table and write all rules again.
     *
     * @param array $rules
     * @return array
     */
    public function create(array $rules = array())
    {
        $connection = $this->squarePricingTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {
            $adapter = $this->squarePricingTable->getAdapter();
            $adapter->query('TRUNCATE TABLE ' . SquarePricingTable::NAME, Adapter::QUERY_MODE_EXECUTE);

            $statement = $adapter->query('INSERT INTO ' . SquarePricingTable::NAME . ' (sid, priority, date_start, date_end, day_start, day_end, time_start, time_end, price, rate, gross, per_time_block) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            foreach ($rules as $rule) {
                if (count($rule) != 12) {
                    throw new InvalidArgumentException('Pricing rules are not well formed internally');
                }

                $statement->execute($rule);
                $transaction = false;
            }

            $connection->commit();

            $this->getEventManager()->trigger('create', $rules);

            return $rules;

        } catch (\Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
    }

    /**
     * Gets all pricings rules.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->rules;
    }

    /**
     * Determines the pricing rule for the passed square and time.
     *
     * @param string|DateTime $dateTime
     * @param int|Square $square
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getPricingRule($dateTime, $square)
    {
        $matchedRule = null;

        if (is_string($dateTime)) {
            $dateTimeParts = explode(' ', $dateTime);

            if (! count($dateTimeParts) == 2) {
                throw new InvalidArgumentException('The passed datetime string is invalid');
            }

            $date = $dateTimeParts[0];
            $time = $dateTimeParts[1];

            if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
                throw new InvalidArgumentException('The passed date string is invalid');
            }

            if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $time)) {
                throw new InvalidArgumentException('The passed time string is invalid');
            }

            $dateTime = new DateTime($date . ' ' . $time);
        }

        if (! ($dateTime instanceof DateTime)) {
            throw new InvalidArgumentException('The passed datetime type is invalid');
        }

        if ($square instanceof Square) {
            $square = $square->need('sid');
        }

        if (! (is_numeric($square) && $square > 0)) {
            throw new InvalidArgumentException('The passed square is invalid');
        }

        foreach ($this->rules as $rule) {
            $dateStart = new DateTime($rule['date_start']);
            $dateEnd = new DateTime($rule['date_end']);

            if ($dateTime <= $dateEnd && $dateTime >= $dateStart) {
                $dateTimeDay = ($dateTime->format('w') + 6) % 7;

                if ($dateTimeDay <= $rule['day_end'] && $dateTimeDay >= $rule['day_start']) {
                    $ruleDateTime = clone $dateTime;

                    $timeStart = explode(':', $rule['time_start']);
                    $ruleDateTime->setTime($timeStart[0], $timeStart[1]);

                    if ($dateTime >= $ruleDateTime) {
                        $timeEnd = explode(':', $rule['time_end']);
                        $ruleDateTime->setTime($timeEnd[0], $timeEnd[1]);

                        if ($dateTime < $ruleDateTime) {
                            if (is_null($rule['sid']) || $rule['sid'] == $square) {
                                $matchedRule = $rule;
                            }
                        }
                    }
                }
            }
        }

        return $matchedRule;
    }

    /**
     * Determines the pricing rules for the passed square and time interval.
     *
     * @param string|DateTime $date
     * @param string|DateTime $timeStart
     * @param string|DateTime $timeEnd
     * @param int|Square $square
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPricingRules($date, $timeStart, $timeEnd, $square)
    {
        $matchedRules = array();

        if (is_string($date)) {
            if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
                throw new InvalidArgumentException('The passed date string is invalid');
            }

            $date = new DateTime($date);
        }

        if (! ($date instanceof DateTime)) {
            throw new InvalidArgumentException('The passed date type is invalid');
        }

        /* Determine DateTime for the start of the interval */

        $dateTimeStart = clone $date;

        if (is_string($timeStart)) {
            if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeStart)) {
                throw new InvalidArgumentException('The passed start time string is invalid');
            }

            $timeParts = explode(':', $timeStart);
            $dateTimeStart->setTime($timeParts[0], $timeParts[1]);
        } else if ($timeStart instanceof DateTime) {
            $dateTimeStart->setTime($timeStart->format('H'), $timeStart->format('i'));
        } else {
            throw new InvalidArgumentException('The passed start time is invalid');
        }

        /* Determine DateTime for the end of the interval */

        $dateTimeEnd = clone $date;

        if (is_string($timeEnd)) {
            if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeEnd)) {
                throw new InvalidArgumentException('The passed end time string is invalid');
            }

            $timeParts = explode(':', $timeEnd);
            $dateTimeEnd->setTime($timeParts[0], $timeParts[1]);
        } else if ($timeEnd instanceof DateTime) {
            $dateTimeEnd->setTime($timeEnd->format('H'), $timeEnd->format('i'));
        } else {
            throw new InvalidArgumentException('The passed end time is invalid');
        }

        /* Determine the square bookable time block */

        if (! ($square instanceof Square)) {
            $square = $this->squareManager->get($square);
        }

        $timeBlockBookable = $square->need('time_block_bookable');

        /* Check dates and start looping through the rules */

        if ($dateTimeStart > $dateTimeEnd) {
            throw new InvalidArgumentException('The passed time is invalid');
        }

        $lastSpid = null;
        $lastTimeStart = $timeStart->format('H:i');

        while ($dateTimeStart < $dateTimeEnd) {
            $rule = $this->getPricingRule($dateTimeStart, $square);

            if ($rule) {
                if ($lastSpid && $lastSpid !== $rule->spid) {
                    $lastTimeStart = $dateTimeStart->format('H:i');
                }

                $lastSpid = $rule->spid;

                $rule->time_start_interval = $lastTimeStart;

                $dateTimeStart->modify('+' . $timeBlockBookable . ' sec');
                $rule->time_end_interval = $dateTimeStart->format('H:i');
                $dateTimeStart->modify('-' . $timeBlockBookable . ' sec');

                $matchedRules[$rule->spid] = $rule;
            }

            $dateTimeStart->modify('+' . $timeBlockBookable . ' sec');
        }

        return $matchedRules;
    }

    /**
     * Determines the final calculated pricing for the passed square and time interval.
     *
     * @param string|DateTime $date
     * @param string|DateTime $timeStart
     * @param string|DateTime $timeEnd
     * @param int|Square $square
     * @return array
     * @throws InvalidArgumentException
     */
    public function getFinalPricing($date, $timeStart, $timeEnd, $square, $quantity)
    {
        $pricing = array();

        if (is_string($date)) {
            if (! preg_match('/^([0-9]{4})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
                throw new InvalidArgumentException('The passed date string is invalid');
            }

            $date = new DateTime($date);
        }

        if (! ($date instanceof DateTime)) {
            throw new InvalidArgumentException('The passed date type is invalid');
        }

        /* Determine DateTime for the start of the interval */

        $dateTimeStart = clone $date;

        if (is_string($timeStart)) {
            if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeStart)) {
                throw new InvalidArgumentException('The passed start time string is invalid');
            }

            $timeParts = explode(':', $timeStart);
            $dateTimeStart->setTime($timeParts[0], $timeParts[1]);
        } else if ($timeStart instanceof DateTime) {
            $dateTimeStart->setTime($timeStart->format('H'), $timeStart->format('i'));
        } else {
            throw new InvalidArgumentException('The passed start time is invalid');
        }

        /* Determine DateTime for the end of the interval */

        $dateTimeEnd = clone $date;

        if (is_string($timeEnd)) {
            if (! preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $timeEnd)) {
                throw new InvalidArgumentException('The passed end time string is invalid');
            }

            $timeParts = explode(':', $timeEnd);
            $dateTimeEnd->setTime($timeParts[0], $timeParts[1]);
        } else if ($timeEnd instanceof DateTime) {
            $dateTimeEnd->setTime($timeEnd->format('H'), $timeEnd->format('i'));
        } else {
            throw new InvalidArgumentException('The passed end time is invalid');
        }

        /* Determine the square bookable time block */

        if (! ($square instanceof Square)) {
            $square = $this->squareManager->get($square);
        }

        $timeBlockBookable = $square->get('time_block_bookable', $square->need('time_block'));

        /* Check dates and start looping through the rules */

        if ($dateTimeStart > $dateTimeEnd) {
            throw new InvalidArgumentException('The passed time is invalid');
        }

        while ($dateTimeStart < $dateTimeEnd) {
            $rule = $this->getPricingRule($dateTimeStart, $square);

            if ($rule) {
                if (! isset($pricing['price'])) {
                    $pricing['price'] = 0;
                }

                if (! isset($pricing['rate'])) {
                    $pricing['rate'] = $rule->rate;
                } else {
                    if ($pricing['rate'] != $rule->rate) {
                        throw new RuntimeException('Pricing rates must be consistent');
                    }
                }

                if (! isset($pricing['gross'])) {
                    $pricing['gross'] = $rule->gross;
                } else {
                    if ($pricing['gross'] != $rule->gross) {
                        throw new RuntimeException('Pricing gross must be consistent');
                    }
                }

                if (! isset($pricing['seconds'])) {
                    $pricing['seconds'] = 0;
                }

                if (! isset($pricing['per_quantity'])) {
                    $pricing['per_quantity'] = $rule->per_quantity;
                } else {
                    if ($pricing['per_quantity'] != $rule->per_quantity) {
                        throw new RuntimeException('Pricing per quantity must be consistent');
                    }
                }

                /* Calculate influence of this rule for the current time block */

                $influence = $timeBlockBookable / $rule->per_time_block;

                /* Calculate price including tax rate */

                $price = $rule->price * $influence;

                if (! $rule->gross) {
                    $price += $price * ($rule->rate / 100);
                }

                /* Multiply by quantity if required */

                if ($rule->per_quantity) {
                    $price *= $quantity;
                }

                $pricing['price'] += $price;

                /* Sum up time in seconds */

                $pricing['seconds'] += $timeBlockBookable;
            }

            $dateTimeStart->modify('+' . $timeBlockBookable . ' sec');
        }

        if (isset($pricing['price'])) {
            $pricing['price'] = round($pricing['price']);
        }

        return $pricing;
    }

    /**
     * Determines the final calculated pricing for the passed square and datetime interval.
     *
     * @param DateTime $dateTimeStart
     * @param DateTime $dateTimeEnd
     * @param Square $square
     * @param int $quantity
     * @return array
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function getFinalPricingInRange(DateTime $dateTimeStart, DateTime $dateTimeEnd, Square $square, $quantity)
    {
        if ($dateTimeStart > $dateTimeEnd) {
            throw new InvalidArgumentException('The passed date range is invalid');
        }

        $finalPricingInRange = array();

        $days = $dateTimeEnd->format('z') - $dateTimeStart->format('z');

        $walkingDate = clone $dateTimeStart;
        $walkingDate->setTime(0, 0);
        $walkingDateLimit = clone $dateTimeEnd;
        $walkingDateLimit->setTime(0, 0);
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

            $finalPricing = $this->getFinalPricing($walkingDate, $walkingTimeStart, $walkingTimeEnd, $square, $quantity);

            if ($finalPricing) {
                if (! isset($finalPricingInRange['price'])) {
                    $finalPricingInRange['price'] = $finalPricing['price'];
                } else {
                    $finalPricingInRange['price'] += $finalPricing['price'];
                }

                if (! isset($finalPricingInRange['rate'])) {
                    $finalPricingInRange['rate'] = $finalPricing['rate'];
                } else {
                    if ($finalPricingInRange['rate'] != $finalPricing['rate']) {
                        throw new RuntimeException('Pricing rates must be consistent');
                    }
                }

                if (! isset($finalPricingInRange['gross'])) {
                    $finalPricingInRange['gross'] = $finalPricing['gross'];
                } else {
                    if ($finalPricingInRange['gross'] != $finalPricing['gross']) {
                        throw new RuntimeException('Pricing gross must be consistent');
                    }
                }

                if (! isset($finalPricingInRange['seconds'])) {
                    $finalPricingInRange['seconds'] = $finalPricing['seconds'];
                } else {
                    $finalPricingInRange['seconds'] += $finalPricing['seconds'];
                }

                if (! isset($finalPricingInRange['per_quantity'])) {
                    $finalPricingInRange['per_quantity'] = $finalPricing['per_quantity'];
                } else {
                    if ($finalPricingInRange['per_quantity'] != $finalPricing['per_quantity']) {
                        throw new RuntimeException('Pricing per quantity must be consistent');
                    }
                }
            }

            $walkingDate->modify('+1 day');
            $walkingDateIndex++;
        }

        return $finalPricingInRange;
    }

}
