<?php

namespace Booking\Manager\Booking;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BillManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new BillManager($sm->get('Booking\Table\Booking\BillTable'));
    }

}