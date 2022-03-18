<?php

namespace Square\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PricingSummaryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new PricingSummary(
            $sm->getServiceLocator()->get('Base\Manager\OptionManager'),
            $sm->getServiceLocator()->get('Square\Manager\SquarePricingManager'),
            $sm->getServiceLocator()->get('User\Manager\UserSessionManager'));
    }

}