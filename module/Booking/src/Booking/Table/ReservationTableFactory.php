<?php

namespace Booking\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReservationTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new ReservationTable(ReservationTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}