<?php

namespace Base\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailTransportServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new MailTransportService($sm->get('Base\Manager\ConfigManager'));
    }

}