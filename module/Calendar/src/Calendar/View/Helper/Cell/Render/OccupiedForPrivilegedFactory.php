<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OccupiedForPrivilegedFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new OccupiedForPrivileged($sm->getServiceLocator()->get('Booking\Service\BookingStatusService'));
    }

}
