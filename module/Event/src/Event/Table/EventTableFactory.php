<?php

namespace Event\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EventTable(EventTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}