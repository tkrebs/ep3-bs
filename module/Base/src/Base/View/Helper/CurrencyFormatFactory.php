<?php

namespace Base\View\Helper;

use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CurrencyFormatFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->getServiceLocator()->get('Base\Manager\ConfigManager');

        $locale = $configManager->need('i18n.locale');
        $currency = $configManager->need('i18n.currency');

        $currencyFormat = new CurrencyFormat();
        $currencyFormat->setCurrencyCode($currency);
        $currencyFormat->setLocale($locale);

        return $currencyFormat;
    }

}