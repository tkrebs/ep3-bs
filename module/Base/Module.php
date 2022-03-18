<?php

namespace Base;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Validator\AbstractValidator;

class Module implements AutoloaderProviderInterface, BootstrapListenerInterface, ConfigProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /* Check database */

        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        $dbConnection = $dbAdapter->getDriver()->getConnection();

        try {
            $dbConnection->connect();
        } catch (\RuntimeException $e) {
            include 'Charon.php';

            Charon::carry('application', 'configuration', 1);
        }

        /* Set global validator translator */

        $translator = $serviceManager->get('Translator');
        AbstractValidator::setDefaultTranslator($translator);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

}