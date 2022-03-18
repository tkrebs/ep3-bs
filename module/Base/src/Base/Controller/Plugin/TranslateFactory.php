<?php

namespace Base\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TranslateFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Translate($sm->getServiceLocator()->get('Translator'));
    }

}