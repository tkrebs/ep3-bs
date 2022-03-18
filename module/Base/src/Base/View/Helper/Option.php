<?php

namespace Base\View\Helper;

use Base\Manager\OptionManager;
use RuntimeException;
use Zend\View\Helper\AbstractHelper;

class Option extends AbstractHelper
{

    protected $optionManager;

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    public function __invoke($key, $default = null, $separator = ' ')
    {
        if (is_array($key)) {
            $values = array();

            foreach ($key as $index => $item) {
                if (is_numeric($index)) {
                    if ($value = $this->optionManager->get($item)) {
                        $values[] = $value;
                    }
                } else {
                    if ($value = $this->optionManager->get($index)) {
                        $values[] = $value;
                    } else {
                        if ($item === false) {
                            throw new RuntimeException( sprintf($this->getView()->translate('Option %s does not exist'), $index) );
                        }

                        $values[] = $item;
                    }
                }
            }

            return implode($separator, $values);
        } else {
            return $this->optionManager->get($key, $default);
        }
    }

}