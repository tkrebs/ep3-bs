<?php

namespace Square\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->get('Base\Manager\ConfigManager');

        $locale = $configManager->need('i18n.locale');

        return new SquareManager(
            $sm->get('Square\Table\SquareTable'),
            $sm->get('Square\Table\SquareMetaTable'),
            $locale);
    }

}