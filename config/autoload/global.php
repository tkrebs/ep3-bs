<?php
/**
 * Global application configuration
 *
 * Usually, you can leave this file as is
 * and do not need to worry about its contents.
 */

return [
    'db' => [
        'driver' => 'pdo_mysql',
        'charset' => 'UTF8',
    ],
    'cookie_config' => [
        'cookie_name_prefix' => 'ep3-bs',
    ],
    'redirect_config' => [
        'cookie_name' => 'ep3-bs-origin',
        'default_origin' => 'frontend',
    ],
    'session_config' => [
        'name' => 'ep3-bs-session',
        'save_path' => getcwd() . '/data/session/',
        'use_cookies' => true,
        'use_only_cookies' => true,
    ],
];
