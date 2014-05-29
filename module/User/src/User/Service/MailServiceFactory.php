<?php

namespace User\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $mailService = $sm->get('Base\Service\MailService');
        $configManager = $sm->get('Base\Manager\ConfigManager');
        $optionManager = $sm->get('Base\Manager\OptionManager');

        return new MailService($mailService, $configManager, $optionManager);
    }

}