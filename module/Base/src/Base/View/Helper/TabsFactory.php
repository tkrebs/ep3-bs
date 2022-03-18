<?php

namespace Base\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TabsFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Tabs( $sm->getServiceLocator()->get('Request')->getUri()->getPath() );
    }

}