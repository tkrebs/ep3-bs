<?php

namespace Base\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PriceFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new PriceFormat($sm->getServiceLocator()->get('Base\Manager\OptionManager'));
    }

}