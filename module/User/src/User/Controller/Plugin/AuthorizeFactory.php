<?php

namespace User\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizeFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new Authorize($sm->getServiceLocator()->get('User\Manager\UserSessionManager'));
    }

}