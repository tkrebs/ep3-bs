<?php

namespace Square\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareProductTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquareProductTable(SquareProductTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}