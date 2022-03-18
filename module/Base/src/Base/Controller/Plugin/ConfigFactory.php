<?php

namespace Base\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Config($sm->getServiceLocator()->get('Base\Manager\ConfigManager'));
    }

}