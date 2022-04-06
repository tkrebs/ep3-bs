<?php

namespace Backend\View\Helper\Square;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareGroupFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquareGroupFormat($sm->getServiceLocator()->get('Square\Manager\SquareManager'));
    }

}