<?php

namespace Backend\Controller\Plugin\Booking;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UpdateFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $serviceManager = $sm->getServiceLocator();

        return new Update(
            $serviceManager->get('Booking\Manager\BookingManager'),
            $serviceManager->get('Booking\Manager\ReservationManager'),
            $serviceManager->get('Square\Manager\SquareManager'),
            $serviceManager->get('User\Manager\UserManager'),
            $serviceManager->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection());
    }

}