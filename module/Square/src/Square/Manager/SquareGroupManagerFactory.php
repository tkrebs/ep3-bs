<?php

namespace Square\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareGroupManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->get('Base\Manager\ConfigManager');

        $locale = $configManager->need('i18n.locale');

        return new SquareGroupManager($sm->get('Square\Table\SquareGroupTable'), $locale);
    }

}