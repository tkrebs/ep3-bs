<?php

namespace Base\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OptionManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->get('Base\Manager\ConfigManager');

        $locale = $configManager->need('i18n.locale');

        return new OptionManager($sm->get('Base\Table\OptionTable'), $locale);
    }

}