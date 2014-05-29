<?php

namespace Base\View\Helper\Layout;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HeaderLocaleChoiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new HeaderLocaleChoice(
            $sm->getServiceLocator()->get('Base\Manager\ConfigManager'),
            $sm->getServiceLocator()->get('Request')->getUri());
    }

}