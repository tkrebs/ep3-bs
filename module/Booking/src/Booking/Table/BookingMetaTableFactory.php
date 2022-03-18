<?php

namespace Booking\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BookingMetaTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new BookingMetaTable(BookingMetaTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}