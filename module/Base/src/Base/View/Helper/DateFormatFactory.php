<?php

namespace Base\View\Helper;

use Zend\I18n\View\Helper\DateFormat;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DateFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->getServiceLocator()->get('Base\Manager\ConfigManager');
        
        $locale = $configManager->need('i18n.locale');

        $dateFormat = new DateFormat();
        $dateFormat->setLocale($locale);

        return $dateFormat;
    }

}