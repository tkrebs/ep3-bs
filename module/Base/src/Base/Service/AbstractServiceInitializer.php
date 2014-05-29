<?php

namespace Base\Service;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractServiceInitializer implements InitializerInterface
{

    public function initialize($instance, ServiceLocatorInterface $sm)
    {
        if ($instance instanceof AbstractService) {
            $instance->setTranslator($sm->get('Translator'));
        }
    }

}