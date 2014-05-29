<?php

namespace Base\Manager\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigLocaleListenerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new ConfigLocaleListener($sm->get('Request'));
    }

}