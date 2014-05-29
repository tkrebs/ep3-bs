<?php

namespace Base\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DateFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new DateFormat($sm->getServiceLocator()->get('ViewHelperManager')->get('DateFormat'));
    }

}