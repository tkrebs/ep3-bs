<?php

namespace User\Manager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserSessionManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new UserSessionManager(
            $sm->get('User\Manager\UserManager'),
            $sm->get('Zend\Session\SessionManager'));
    }

}