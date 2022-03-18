<?php

namespace Base\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Config($sm->getServiceLocator()->get('Base\Manager\ConfigManager'));
    }

}