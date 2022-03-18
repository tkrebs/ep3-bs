<?php

namespace Booking\Entity\Booking;

use Base\Entity\AbstractEntity;

class Bill extends AbstractEntity
{

    protected $bbid;
    protected $bid;
    protected $description;
    protected $quantity;
    protected $time;
    protected $price;
    protected $rate;
    protected $gross;

}