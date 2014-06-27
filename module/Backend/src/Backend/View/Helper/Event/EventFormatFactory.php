<?php

namespace Backend\View\Helper\Event;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EventFormat($sm->getServiceLocator()->get('Square\Manager\SquareManager'));
    }

}