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

        return self::$statusOptions[$status] ?? 'Unknown';
    }

}
