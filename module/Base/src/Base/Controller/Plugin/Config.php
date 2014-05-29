<?php

namespace Base\Controller\Plugin;

use Base\Manager\ConfigManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Config extends AbstractPlugin
{

    protected $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function __invoke($key, $default = null)
    {
        return $this->configManager->get($key, $default);
    }

}