<?php

namespace Square\Entity;

use Base\Entity\AbstractLocaleEntity;

class Square extends AbstractLocaleEntity
{

    protected $sid;
    protected $name;
    protected $status;
    protected $priority;
    protected $capacity;
    protected $capacity_heterogenic;
    protected $allow_notes;
    protected $time_start;
    protected $time_end;
    protected $time_block;
    protected $time_block_bookable;
    protected $time_block_bookable_max;
    protected $min_range_book;
    protected $range_book;
    protected $max_active_bookings;
    protected $range_cancel;

    /**
     * The possible status options.
     */
    public static $statusOptions = array(
        'disabled' => 'Disabled',
        'readonly' => 'Read-Only',
        'enabled' => 'Enabled',
    );

    /**
     * Returns the status string.
     */
    public function getStatus()
    {
        $status = $this->need('status');

        return self::$statusOptions[$status] ?? 'Unknown';
    }

}
