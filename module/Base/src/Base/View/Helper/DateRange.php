<?php

namespace Base\View\Helper;

use DateTime;
use IntlDateFormatter;
use Zend\View\Helper\AbstractHelper;

class DateRange extends AbstractHelper
{

    public function __invoke(DateTime $dateTimeStart, DateTime $dateTimeEnd)
    {
        $view = $this->getView();

        if ($dateTimeStart->format('Y-m-d') == $dateTimeEnd->format('Y-m-d')) {

            return sprintf('%s, %s',
                $view->dateFormat($dateTimeStart, IntlDateFormatter::MEDIUM),
                $view->timeRange($dateTimeStart, $dateTimeEnd, '%s to %s'));

        } else {

            $formatStart = $view->dateFormat($dateTimeStart, IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
            $formatEnd = $view->dateFormat($dateTimeEnd, IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

            $locale = $view->config('i18n.locale');

            if ($locale == 'de_DE' || $locale == 'de-DE') {
                $formatEnd .= ' Uhr';
            }

            return sprintf($view->t('%s to %s'), $formatStart, $formatEnd);
        }
    }

}