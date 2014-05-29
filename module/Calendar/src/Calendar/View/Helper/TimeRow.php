<?php

namespace Calendar\View\Helper;

use Zend\View\Helper\AbstractHelper;

class TimeRow extends AbstractHelper
{

    public function __invoke($timeStart, $timeEnd, $timeBlock, $timeBlockCount)
    {
        if ($timeBlockCount == 1) {
            return null;
        }

        $view = $this->getView();
        $html = '';

        $html .= '<tr class="calendar-time-row">';

        $colWidth = floor(100 / $timeBlockCount);

        for ($walkingTime = $timeStart; $walkingTime < $timeEnd; $walkingTime += $timeBlock) {
            $html .= sprintf('<td style="width: %s%%;">', $colWidth);

            $html .= sprintf('<div class="cts-label">%s</div> <div class="cte-label">%s %s</div>',
                $view->timeFormat($walkingTime, false, 'UTC'), $view->translate('to'), $view->timeFormat($walkingTime + $timeBlock, true, 'UTC', true));

            $html .= '</td>';
        }

        $html .= '</tr>';

        return $html;
    }

}