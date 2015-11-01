<?php
/**
 * Global application configuration
 *
 * Usually, you can leave this file as is and don't
 * need to worry about its contents.
 */

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('error_log', getcwd() . '/data/log/errors.txt');

ini_set('display_errors', EP3_BS_DEV ? 1 : 0);
ini_set('display_startup_errors', EP3_BS_DEV ? 1 : 0);
ini_set('log_errors', EP3_BS_DEV ? 0 : 1);
ini_set('ignore_repeated_errors', 1);
ini_set('html_errors',  EP3_BS_DEV ? 1 : 0);
ini_set('ignore_user_abort', EP3_BS_DEV ? 1 : 0);

ini_set('default_charset', 'UTF-8');

return array(
    'db' => array(
        'driver' => 'pdo_mysql',
        'charset' => 'UTF8',
    ),
    'cookie_config' => array(
        'cookie_name_prefix' => 'ep3-bs',
    ),
    'redirect_config' => array(
        'cookie_name' => 'ep3-bs-origin',
        'default_origin' => 'frontend',
    ),
    'session_config' => array(
        'name' => 'ep3-bs-session',
        'save_path' => getcwd() . '/data/session/',
        'use_cookies' => true,
        'use_only_cookies' => true,
    ),
    'i18n' => array(
        'choice' => array(
            'en-US' => 'English',
            'de-DE' => 'Deutsch',
        ),
    ),
);
