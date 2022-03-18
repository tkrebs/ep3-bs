<?php

namespace Event\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventMetaTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EventMetaTable(EventMetaTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}