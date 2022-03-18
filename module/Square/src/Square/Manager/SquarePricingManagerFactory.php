<?php

namespace Square\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquarePricingManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquarePricingManager(
            $sm->get('Square\Table\SquarePricingTable'),
            $sm->get('Square\Manager\SquareManager'));
    }

}