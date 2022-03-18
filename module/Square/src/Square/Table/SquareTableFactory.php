<?php

namespace Square\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquareTable(SquareTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}