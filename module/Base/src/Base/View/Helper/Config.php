<?php

namespace Base\View\Helper;

use Base\Manager\ConfigManager;
use Zend\View\Helper\AbstractHelper;

class Config extends AbstractHelper
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