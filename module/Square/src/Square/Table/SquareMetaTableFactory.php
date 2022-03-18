<?php

namespace Square\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareMetaTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquareMetaTable(SquareMetaTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}