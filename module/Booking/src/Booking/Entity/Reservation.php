<?php

namespace Booking\Entity;

use Base\Entity\AbstractEntity;

class Reservation extends AbstractEntity
{

    protected $rid;
    protected $bid;
    protected $date;
    protected $time_start;
    protected $time_end;

}