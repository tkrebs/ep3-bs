<?php

namespace Square\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TimeBlockChoiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new TimeBlockChoice(
            $sm->getServiceLocator()->get('Booking\Manager\BookingManager'),
            $sm->getServiceLocator()->get('Booking\Manager\ReservationManager'));
    }

}