<?php
/**
 * ep-3 Bookingsystem Setup Tool
 *
 * This tool helps to initialize and setup the database.
 */

ob_start();

chdir(dirname(__DIR__));

/**
 * Quickly check the current PHP version.
 */
if (version_compare(PHP_VERSION, '8.1.0') < 0) {
    exit('PHP 8.1+ is required (currently running PHP ' . PHP_VERSION . ')');
}

/**
 * Quickly check if the intl extension is installed.
 */
if (! extension_loaded('intl')) {
	exit('The PHP <a href="http://php.net/manual/de/book.intl.php">intl extension</a> is required but not installed. '
	   . 'Please contact your web hosting provider to get this one fixed.');
}

/**
 * We are using composer (getcomposer.org) to install and autoload the dependencies.
 * Composer will create the entire vendor directory for us, including the autoloader.
 */
$autoloader = 'vendor/autoload.php';

if (! is_readable($autoloader)) {

    $charon = 'module/Base/Charon.php';

    if (! is_readable($charon)) {
        exit('Base module not found');
    }

    /**
     * Display an informative error page.
     */
    require $charon;

    Base\Charon::carry('application', 'installation', 1);
}

/**
 * Load and prepare the autoloader.
 */
require $autoloader;

/**
 * Initialize our PHP environment.
 */
$init = 'config/init.php';

if (! is_readable($init)) {
    exit('Please rename <b>config/init.php.dist</b> to <b>config/init.php</b> and edit its options accordingly');
}

/**
 * Initialize our application with the setup configuration file and run!
 */
Zend\Mvc\Application::init(require 'config/setup.php')->run();
