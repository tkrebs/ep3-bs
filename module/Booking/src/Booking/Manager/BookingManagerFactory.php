<?php

namespace Booking\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BookingManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new BookingManager(
            $sm->get('Booking\Table\BookingTable'),
            $sm->get('Booking\Table\BookingMetaTable'));
    }

}