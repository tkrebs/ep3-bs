<?php

namespace Backend\Controller\Plugin\Booking;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DetermineParamsFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new DetermineParams(
            $sm->getServiceLocator()->get('Booking\Manager\BookingManager'),
            $sm->getServiceLocator()->get('Booking\Manager\ReservationManager'),
            $sm->getServiceLocator()->get('Square\Manager\SquareManager'),
            $sm->getServiceLocator()->get('User\Manager\UserManager'));
    }

}