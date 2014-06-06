<?php

namespace Square\Entity;

use Base\Entity\AbstractEntity;

class SquareProduct extends AbstractEntity
{

    protected $spid;
    protected $sid;
    protected $priority;
    protected $date_start;
    protected $date_end;
    protected $name;
    protected $description;
    protected $options;
    protected $price;
    protected $rate;
    protected $gross;
    protected $locale;

}