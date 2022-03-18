<?php

namespace Booking\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReservationManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new ReservationManager(
            $sm->get('Booking\Table\ReservationTable'),
            $sm->get('Booking\Table\ReservationMetaTable'),
            $sm->get('Square\Manager\SquareManager'));
    }

}