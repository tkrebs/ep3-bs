<?php

namespace Backend\Form\User;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EditForm($sm->getServiceLocator()->get('User\Manager\UserManager'));
    }

}