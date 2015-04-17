<?php
/**
 *	Filemanager PHP connector
 *  This file should at least declare auth() function
 *  and instantiate the Filemanager as '$fm'
 *
 *  IMPORTANT : by default Read and Write access is granted to everyone
 *  Copy/paste this file to 'user.config.php' file to implement your own auth() function
 *  to grant access to wanted users only
 *
 *	filemanager.php
 *	use for ckeditor filemanager
 *
 *	@license	MIT License
 *  @author		Simon Georget <simon (at) linea21 (dot) com>
 *	@copyright	Authors
 */



/**
 *	Check if user is authorized
 *
 *	@return boolean true if access granted, false if no access
 */
function auth() {

  $cwd = getcwd();

  chdir(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

  if (! defined('EP3_BS_DEV')) {
    define('EP3_BS_DEV', false);
  }

  $config = require 'config/autoload/global.php';

  $sessionName = $config['session_config']['name'];
  $sessionPath = $config['session_config']['save_path'];

  if (isset($_COOKIE[$sessionName])) {

    require 'vendor/zendframework/zendframework/library/Zend/Stdlib/Exception/ExceptionInterface.php';
    require 'vendor/zendframework/zendframework/library/Zend/Stdlib/Exception/InvalidArgumentException.php';
    require 'vendor/zendframework/zendframework/library/Zend/Stdlib/ArrayObject.php';

    session_name($sessionName);
    session_save_path($sessionPath);

    session_start();

    chdir($cwd);

    if (isset($_SESSION['UserSession'])) {

        $userSession = $_SESSION['UserSession'];

        if ($userSession && $userSession instanceof Zend\Stdlib\ArrayObject) {
            if ($userSession->uid && is_numeric($userSession->uid) && $userSession->uid > 0) {
	            if ($userSession->status && ($userSession->status == 'assist' || $userSession->status == 'admin')) {
		            return true;
	            }
            }
        }
    }
  }

  return false;
}

// we instantiate the Filemanager
$fm = new Filemanager();
