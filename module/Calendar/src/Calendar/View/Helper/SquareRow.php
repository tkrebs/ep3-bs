<?php

namespace Calendar\View\Helper;

use Zend\View\Helper\AbstractHelper;

class SquareRow extends AbstractHelper
{

    public function __invoke(array $squares, $squaresCount, $outerClasses = null)
    {
        $view = $this->getView();
        $html = '';

        $html .= sprintf('<tr class="calendar-square-row %s">',
            $outerClasses);

        $colWidth = floor(100 / $squaresCount);

        foreach ($squares as $square) {
            $html .= sprintf('<td style="width: %s%%;"><div class="square-label">%s</div></td>',
                $colWidth, $view->t($square->need('name')));
        }

        $html .= '</tr>';

        return $html;
    }

}