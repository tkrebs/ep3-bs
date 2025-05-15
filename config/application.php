<?php
/**
 * Application configuration bootstrap
 */

/**
 * Development mode
 *
 * Should be controlled via TAG constant in the init.php
 */
if (defined('EP3_BS_DEV_TAG')) {
    define('EP3_BS_DEV', EP3_BS_DEV_TAG);
} else {
    define('EP3_BS_DEV', true);
}

/**
 * Application configuration array
 */
return [
    'modules' => array_merge([

        /**
         * Application core modules
         *
         * Usually, you don't have to change these
         * (but you can, of course ;)
         */
        'Backend',
        'Base',
        'Booking',
        'Calendar',
        'Event',
        'Frontend',
        'Service',
        'Square',
        'User',

        /**
         * Custom modules
         *
         * Place your own, custom or third party modules in the modulex/ directory
         * and they will be loaded automatically.
         */
    ], include 'modulexes.php'),

    /**
     * Some further internal settings,
     * don't worry about these.
     */
    'module_listener_options' => [
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            'module',
            'modulex',
            'vendor',
        ],
        'config_cache_enabled' => ! EP3_BS_DEV,
        'config_cache_key' => 'ep3-bs',
        'module_map_cache_enabled' => ! EP3_BS_DEV,
        'module_map_cache_key' => 'ep3-bs',
        'cache_dir' => getcwd() . '/data/cache/',
    ],
];
