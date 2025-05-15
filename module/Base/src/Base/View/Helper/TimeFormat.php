<?php

namespace Base\View\Helper;

use DateTime;
use IntlDateFormatter;
use RuntimeException;
use Zend\View\Helper\AbstractHelper;

class TimeFormat extends AbstractHelper
{

    public function __invoke($time, $postfix = true, $timezone = null, $transform = false)
    {
        if (is_numeric($time)) {
            $timestamp = $time;

            $time = new DateTime();
            $time->setTimestamp($timestamp);
        }

        if (is_string($time)) {
            if (preg_match('/^(00|0?[1-9]|1[0-9]|2[0-4])\:(00|0[0-9]|[1-5][0-9])(\:(00|0[0-9]|[1-5][0-9]))?$/', $time)) {
                $timeParts = explode(':', $time);

                switch (count($timeParts)) {
                    case 2:
                        $time = new DateTime();
                        $time->setTime($timeParts[0], $timeParts[1]);
                        break;
                    case 3:
                        $time = new DateTime();
                        $time->setTime($timeParts[0], $timeParts[1], $timeParts[2]);
                        break;
                }
            }
        }

        if (! ($time instanceof DateTime)) {
            throw new RuntimeException('Invalid time passed to time format');
        }

        $view = $this->getView();

        if ($timezone) {
            $dateFormatPlugin = $view->plugin('DateFormat');
            $dateFormatTimezone = $dateFormatPlugin->getTimezone();
            $dateFormatPlugin->setTimezone($timezone);
        }

        $format = $view->dateFormat($time, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);

        if ($timezone) {
            $dateFormatPlugin->setTimezone($dateFormatTimezone);
        }

        if ($transform) {
            if ($format == '00:00') {
                $format = '24:00';
            }
        }

        if ($postfix) {
            $locale = $view->config('i18n.locale');

            if ($locale == 'de_DE' || $locale == 'de-DE') {
                $format .= ' Uhr';
            }
        }

        return $format;
    }

}