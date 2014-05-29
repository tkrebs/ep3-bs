<?php

namespace Square\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class QuantityChoiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new QuantityChoice($sm->getServiceLocator()->get('Base\Manager\OptionManager'));
    }

}