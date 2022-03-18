<?php

namespace Booking\Entity;

use Base\Entity\AbstractEntityFactory;

class ReservationFactory extends AbstractEntityFactory
{

    protected static $entityClass = 'Booking\Entity\Reservation';
    protected static $entityPrimary = 'rid';

}