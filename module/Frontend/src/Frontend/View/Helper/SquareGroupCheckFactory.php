<?php

namespace Frontend\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareGroupCheckFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquareGroupCheck(
            $sm->getServiceLocator()->get('Square\Manager\SquareManager'));
    }

}