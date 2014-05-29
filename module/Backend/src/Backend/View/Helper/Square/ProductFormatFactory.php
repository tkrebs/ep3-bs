<?php

namespace Backend\View\Helper\Square;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProductFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new ProductFormat($sm->getServiceLocator()->get('Square\Manager\SquareManager'));
    }

}