<?php

namespace User\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new UserTable(UserTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}