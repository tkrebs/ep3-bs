<?php

namespace User\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegistrationFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new RegistrationForm(
            $sm->getServiceLocator()->get('Base\Manager\OptionManager'),
            $sm->getServiceLocator()->get('User\Manager\UserManager'));
    }

}