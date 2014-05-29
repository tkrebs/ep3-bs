<?php

namespace Square\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SquareProductManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->get('Base\Manager\ConfigManager');

        $locale = $configManager->need('i18n.locale');

        return new SquareProductManager($sm->get('Square\Table\SquareProductTable'), $locale);
    }

}