<?php

namespace Calendar\View\Helper\Cell;

use Zend\View\Helper\AbstractHelper;

class CellLink extends AbstractHelper
{

    public function __invoke($content, $url = '#', $outerClasses = null, $innerClasses = null)
    {
        return $this->getView()->calendarCell($content, $outerClasses, $innerClasses, 'a', 'div', sprintf('href="%s"', $url));
    }

}