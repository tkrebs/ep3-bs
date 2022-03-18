<?php

namespace Base\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RedirectBackFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new RedirectBack($sm->getServiceLocator()->get('Base\Manager\ConfigManager'));
    }

}