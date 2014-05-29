<?php

namespace Square\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareValidatorFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new SquareValidator(
            $sm->get('Booking\Manager\BookingManager'),
            $sm->get('Booking\Manager\ReservationManager'),
            $sm->get('Square\Manager\SquareManager'),
            $sm->get('User\Manager\UserSessionManager'));
    }

}