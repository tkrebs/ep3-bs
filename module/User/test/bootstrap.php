<?php

/* Check PHP */

if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    throw new RuntimeException('PHP 5.4+ is required (currently running PHP ' . PHP_VERSION . ')');
}

/* Setup PHP */

ini_set('error_reporting', E_ALL);
ini_set('default_charset', 'UTF-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 0);

ini_set('date.timezone', 'Europe/Berlin');

chdir(dirname(__DIR__, 3));

/* Setup constants */

const EP3_BS_DEV = true;

/* Setup autoloader */

$autoloaderFile = 'vendor/autoload.php';

if (! is_readable($autoloaderFile)) {
    throw new RuntimeException('Composer autoloader is required.');
}

$autoloader = require $autoloaderFile;

/* Setup modules */

$moduleConfig = array(
    'module_listener_options' => array(
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
    'modules' => array(
        'User',
    ),
);

$serviceManager = new Zend\ServiceManager\ServiceManager(new Zend\Mvc\Service\ServiceManagerConfig());
$serviceManager->setService('ApplicationConfig', $moduleConfig);
$serviceManager->get('ModuleManager')->loadModules();
