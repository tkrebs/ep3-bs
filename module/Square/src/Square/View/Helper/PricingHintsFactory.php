<?php

namespace Square\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PricingHintsFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new PricingHints(
            $sm->getServiceLocator()->get('Base\Manager\OptionManager'),
            $sm->getServiceLocator()->get('Square\Manager\SquarePricingManager'),
            $sm->getServiceLocator()->get('User\Manager\UserSessionManager'));
    }

}