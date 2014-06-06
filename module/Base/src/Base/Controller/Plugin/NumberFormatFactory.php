<?php

namespace Base\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NumberFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new NumberFormat($sm->getServiceLocator()->get('ViewHelperManager')->get('NumberFormat'));
    }

}