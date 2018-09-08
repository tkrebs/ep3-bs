<?php

namespace Calendar\View\Helper;

use DateTime;
use IntlDateFormatter;
use Zend\View\Helper\AbstractHelper;

class DateRow extends AbstractHelper
{

    public function __invoke(DateTime $date, $colspan, $outerClasses = null)
    {
        $view = $this->getView();

        $dayName = current(preg_split('/,|\s/', $view->dateFormat($date, IntlDateFormatter::FULL)));
        $dateFormat = $view->dateFormat($date, IntlDateFormatter::LONG);

        return sprintf('<tr class="calendar-date-row %s"><td colspan="%s"><div class="day-label">%s</div><div class="date-label">%s</div></td></tr>',
            $outerClasses, $colspan, $dayName, $dateFormat);
    }

}