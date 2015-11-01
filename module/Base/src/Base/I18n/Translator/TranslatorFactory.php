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

        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/backend.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/base.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/booking.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/calendar.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/frontend.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/service.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/setup.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/square.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n/', '%s/user.php');

        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/backend.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/base.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/booking.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/calendar.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/frontend.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/service.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/setup.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/square.php');
        $translator->addTranslationFilePattern('phparray', getcwd() . '/data/res/i18n-custom/', '%s/user.php');

        $translator->setLocale($locale);

        return new \Zend\Mvc\I18n\Translator($translator);
    }

}
