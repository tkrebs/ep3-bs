<?php

namespace Base\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CookieFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Cookie($sm->getServiceLocator()->get('Base\Manager\ConfigManager'));
    }

}