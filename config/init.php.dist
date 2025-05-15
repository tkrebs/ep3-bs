<?php

/**
 * Testing and development mode
 *
 * If true, errors are displayed.
 * If false, errors are silently logged into the error file.
 *
 * If false, certain caches will be enabled.
 *
 * Should be true during initial testing and false when actually using the system.
 */
const EP3_BS_DEV_TAG = true;

/**
 * Timezone of the people using the system
 *
 * An overview of available timezones can be found here:
 * http://php.net/manual/en/timezones.php
 */
ini_set('date.timezone', 'Europe/Berlin');



/**
 * The following settings are more technical and can usually be ignored.
 */

ini_set('error_reporting', E_ALL & ~E_USER_DEPRECATED);
ini_set('error_log', getcwd() . '/data/log/errors.txt');

ini_set('display_errors', EP3_BS_DEV_TAG ? 1 : 0);
ini_set('display_startup_errors', EP3_BS_DEV_TAG ? 1 : 0);
ini_set('log_errors', EP3_BS_DEV_TAG ? 0 : 1);
ini_set('ignore_repeated_errors', 1);
ini_set('html_errors',  EP3_BS_DEV_TAG ? 1 : 0);
ini_set('ignore_user_abort', EP3_BS_DEV_TAG ? 1 : 0);

ini_set('default_charset', 'UTF-8');
