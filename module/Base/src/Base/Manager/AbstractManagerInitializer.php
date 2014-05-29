<?php

namespace Base\Manager;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractManagerInitializer implements InitializerInterface
{

    public function initialize($instance, ServiceLocatorInterface $sm)
    {
        if ($instance instanceof AbstractManager) {
            $instance->setTranslator($sm->get('Translator'));
        }
    }

}