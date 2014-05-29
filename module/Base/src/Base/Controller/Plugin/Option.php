<?php

namespace Base\Controller\Plugin;

use Base\Manager\OptionManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Option extends AbstractPlugin
{

    protected $optionManager;

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    public function __invoke($key, $default = null)
    {
        return $this->optionManager->get($key, $default);
    }

}