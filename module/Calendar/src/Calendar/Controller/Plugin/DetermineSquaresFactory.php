<?php

namespace Calendar\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DetermineSquaresFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new DetermineSquares($sm->getServiceLocator()->get('Square\Manager\SquareManager'));
    }

}