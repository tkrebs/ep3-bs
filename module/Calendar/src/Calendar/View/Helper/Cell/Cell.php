<?php

namespace Calendar\View\Helper\Cell;

use Zend\View\Helper\AbstractHelper;

class Cell extends AbstractHelper
{

    public function __invoke($content, $outerClasses = null, $innerClasses = null, $outerTag = 'div', $innerTag = 'div', $misc = null)
    {
        $view = $this->getView();
        $html = '';

        if (is_array($outerClasses)) {
            $outerClasses = implode(' ', $outerClasses);
        }

        if (is_array($innerClasses)) {
            $innerClasses = implode(' ', $innerClasses);
        }

        $html .= sprintf('<%1$s %6$s class="calendar-cell %3$s"><%2$s class="cc-label %4$s">%5$s</%2$s></%1$s>',
            $outerTag,
            $innerTag,
            $outerClasses,
            $innerClasses,
            $view->t($content),
            $misc);

        return $html;
    }

}