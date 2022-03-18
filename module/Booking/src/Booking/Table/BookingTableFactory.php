<?php

namespace Booking\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BookingTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new BookingTable(BookingTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}