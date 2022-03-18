<?php

namespace Booking\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReservationMetaTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new ReservationMetaTable(ReservationMetaTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}