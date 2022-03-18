<?php

namespace Base\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OptionFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Option($sm->getServiceLocator()->get('Base\Manager\OptionManager'));
    }

}