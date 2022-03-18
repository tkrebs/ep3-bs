<?php

namespace Square\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquarePricingTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquarePricingTable(SquarePricingTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}