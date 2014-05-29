<?php
/**
 * Setup configuration file
 *
 * Don't worry about this file. It is irrelevant for the actual system
 * and is only used for the first setup.
 */

/**
 * Development mode
 *
 * Should always be true in this context!
 */
define('EP3_BS_DEV', true);

/**
 * Setup configuration array
 */
return array(
    'modules' => array(
        'Base',
        'Setup',
        'Square',
        'User',
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
        'config_cache_enabled' => ! EP3_BS_DEV,
        'config_cache_key' => 'ep3-bs',
        'module_map_cache_enabled' => ! EP3_BS_DEV,
        'module_map_cache_key' => 'ep3-bs',
        'cache_dir' => getcwd() . '/data/cache/',
    ),
);