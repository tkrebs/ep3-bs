<?php

namespace Setup\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ValidateSetupFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new ValidateSetup(
            $sm->getServiceLocator()->get('Base\Manager\OptionManager'),
            $sm->getServiceLocator()->get('User\Manager\UserManager'),
            $sm->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
    }

}