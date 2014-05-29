<?php

namespace Square\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProductChoiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new ProductChoice($sm->getServiceLocator()->get('Square\Manager\SquareProductManager'));
    }

}