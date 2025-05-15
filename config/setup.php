<?php
/**
 * Setup configuration bootstrap
 *
 * Don't worry about this file. It is irrelevant for the actual system
 * and is only used for the first setup.
 */

/**
 * Development mode
 *
 * Should always be enabled during setup
 */
const EP3_BS_DEV = true;

/**
 * Setup configuration array
 */
return [
    'modules' => [
        'Base',
        'Setup',
        'Square',
        'User',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            'module',
            'vendor',
        ],
        'config_cache_enabled' => ! EP3_BS_DEV,
        'config_cache_key' => 'ep3-bs-setup',
        'module_map_cache_enabled' => ! EP3_BS_DEV,
        'module_map_cache_key' => 'ep3-bs-setup',
        'cache_dir' => getcwd() . '/data/cache/',
    ],
];
