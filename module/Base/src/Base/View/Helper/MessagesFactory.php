<?php

namespace Base\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MessagesFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        $serviceManager = $sm->getServiceLocator();

        if ($serviceManager->has('User\Manager\UserSessionManager')) {
            $userSessionManager = $serviceManager->get('User\Manager\UserSessionManager');

            $user = $userSessionManager->getSessionUser();
        } else {
            $user = null;
        }

        $messages = new Messages();

        if ($user) {
            $flashMessenger = $serviceManager
                ->get('ControllerPluginManager')
                ->get('FlashMessenger');

            $messages->setFlashMessenger($flashMessenger);
        }

        return $messages;
    }

}