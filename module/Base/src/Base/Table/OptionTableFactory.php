<?php

namespace Base\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OptionTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new OptionTable(OptionTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}