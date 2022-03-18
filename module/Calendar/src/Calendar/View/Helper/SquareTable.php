<?php

namespace Calendar\View\Helper;

use Zend\View\Helper\AbstractHelper;

class SquareTable extends AbstractHelper
{

    public function __invoke(array $squares, $timeBlockCount)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<table class="calendar-square-table" style="width: 192px;">';
        $html .= '<tr class="calendar-date-row"><td>&nbsp;</td></tr>';

        if ($timeBlockCount > 1) {
            $html .= '<tr class="calendar-time-row"><td>&nbsp;</td></tr>';
        }

        foreach ($squares as $square) {
            $html .= '<tr class="calendar-core-row">';

            $html .= sprintf('<td><div class="square-label">%s</div></td>',
                $view->t($square->need('name')));

            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

}