<?php

namespace Booking\Entity\Booking;

use Base\Entity\AbstractEntityFactory;

class BillFactory extends AbstractEntityFactory
{

    protected static $entityClass = 'Booking\Entity\Booking\Bill';
    protected static $entityPrimary = 'bbid';

}