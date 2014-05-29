<?php

namespace Backend\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new MailService(
            $sm->get('Base\Service\MailService'),
            $sm->get('Base\Manager\ConfigManager'),
            $sm->get('Base\Manager\OptionManager'));
    }

}