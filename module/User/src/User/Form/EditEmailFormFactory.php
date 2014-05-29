<?php

namespace User\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditEmailFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EditEmailForm($sm->getServiceLocator()->get('User\Manager\UserManager'));
    }

}