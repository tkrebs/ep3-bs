<?php

namespace Base\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OptionFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Option($sm->getServiceLocator()->get('Base\Manager\OptionManager'));
    }

}