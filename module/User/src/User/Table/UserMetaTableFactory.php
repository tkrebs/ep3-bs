<?php

namespace User\Table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserMetaTableFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new UserMetaTable(UserMetaTable::NAME, $sm->get('Zend\Db\Adapter\Adapter'));
    }

}