<?php

namespace Event\Entity;

use Base\Entity\AbstractLocaleEntity;

class Event extends AbstractLocaleEntity
{

    protected $eid;
    protected $sid;
    protected $status;
    protected $datetime_start;
    protected $datetime_end;
    protected $capacity;

    protected $primary = 'eid';

    /**
     * The possible status options.
     *
     * @var array
     */
    public static $statusOptions = array(
        'enabled' => 'Enabled',
    );

    /**
     * Returns the status string.
     *
     * @return string
     */
    public function getStatus()
    {
        $status = $this->need('status');

        if (isset(self::$statusOptions[$status])) {
            return self::$statusOptions[$status];
        } else {
            return 'Unknown';
        }
    }

    public static $repeatOptions = array(
        '0' => 'Only once',
        '1' => 'Daily',
        '7' => 'Weekly',
        '28' => 'Monthly',
    );

}