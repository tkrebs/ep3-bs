<?php
/**
 * Global application configuration
 *
 * Usually, you can leave this file as is
 * and do not need to worry about its contents.
 */

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
);
