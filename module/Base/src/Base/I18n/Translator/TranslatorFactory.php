<?php

namespace Base\I18n\Translator;

use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TranslatorFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $configManager = $sm->get('Base\Manager\ConfigManager');

        $locale = $configManager->need('i18n.locale');

        $translator = new Translator();
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/base/', '%s.php');
        $translator->setLocale($locale);

        return new \Zend\Mvc\I18n\Translator($translator);
    }

}