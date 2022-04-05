<?php

namespace Square\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareGroupTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquareGroupTable(SquareGroupTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}