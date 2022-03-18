<?php

namespace Base\Controller\Plugin;

use Base\Manager\ConfigManager;
use RuntimeException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Cookie extends AbstractPlugin
{

    protected $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function set($name, $value)
    {
        if (! (is_string($name) && strlen($name) > 1)) {
            throw new RuntimeException('Invalid cookie name to set');
        }

        if (! is_scalar($value)) {
            throw new RuntimeException('Invalid cookie value to set');
        }

        $fullName = $this->configManager->need('cookie_config.cookie_name_prefix') . '-' . $name;

        setcookie($fullName, $value, 0, '/');
    }

    public function get($name, $default = null)
    {
        if (! (is_string($name) && strlen($name) > 1)) {
            throw new RuntimeException('Invalid cookie name requested');
        }

        $fullName = $this->configManager->need('cookie_config.cookie_name_prefix') . '-' . $name;

        if (isset($_COOKIE[$fullName])) {
            return $_COOKIE[$fullName];
        } else {
            if ($default === false) {
                throw new RuntimeException(sprintf($this->translate('Cookie %s is missing'), $name));
            } else {
                return $default;
            }
        }
    }

    public function need($name)
    {
        return $this->get($name, false);
    }

}