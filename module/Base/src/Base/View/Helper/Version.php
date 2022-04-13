<?php

namespace Base\View\Helper;

use Base\Manager\ConfigManager;
use Zend\View\Helper\AbstractHelper;

class Version extends AbstractHelper
{

    protected $configManager;

    public function __construct(ConfigManager $configManager)
    {

        $this->configManager = $configManager;
    }

    public function __invoke()
    {
        return $this->configManager->get('version');
    }

}