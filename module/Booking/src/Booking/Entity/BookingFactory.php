<?php

namespace Booking\Entity;

use Base\Entity\AbstractEntityFactory;

class BookingFactory extends AbstractEntityFactory
{

    protected static $entityClass = 'Booking\Entity\Booking';
    protected static $entityPrimary = 'bid';

}