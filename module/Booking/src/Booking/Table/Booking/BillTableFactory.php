<?php

namespace Booking\Table\Booking;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BillTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new BillTable(BillTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}