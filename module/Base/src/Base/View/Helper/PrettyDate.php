<?php

namespace Base\View\Helper;

use DateTime;
use IntlDateFormatter;
use RuntimeException;
use Zend\View\Helper\AbstractHelper;

class PrettyDate extends AbstractHelper
{

    protected $now;
    protected $today;

    public function __construct()
    {
        $this->now = new DateTime();
        $this->today = new DateTime();
        $this->today->setTime(0, 0);
    }

    public function __invoke($datetime, $time = true)
    {
        if (is_numeric($datetime)) {
            $date = new DateTime();
            $date->setTimestamp($datetime);
        } else if (is_string($datetime)) {
            $date = new DateTime($datetime);
        } else if ($datetime instanceof DateTime) {
            $date = clone $datetime;
            $date->setTime(0, 0);
        } else {
            throw new RuntimeException('Invalid datetime passed to pretty date');
        }

        $diff = $this->now->diff($datetime);
        $dateDiff = $this->today->diff($date);

        $view = $this->getView();

        // Determine time mode
        if ($diff->format('%R') == '+') {
            $past = false;
        } else {
            $past = true;
        }

        // Prepare time
        if ($time) {
            if ($past) {
                $wording = ' by %s';
            } else {
                $wording = ' at %s';
            }

            $time = sprintf($view->translate($wording), $view->timeFormat($datetime));
        } else {
            $time = null;
        }

        // Print date if farther away than 5 days
        if ($dateDiff->format('%a') > 5) {
            return sprintf($view->translate('On %s'), $view->dateFormat($datetime, IntlDateFormatter::LONG) . $time);
        } else if ($dateDiff->format('%a') > 1) {
            if ($past) {
                return sprintf($view->translate('On last %s'), $view->translate($datetime->format('l')) . $time);
            } else {
                return sprintf($view->translate('On next %s'), $view->translate($datetime->format('l')) . $time);
            }
        }

        // Print "tomorrow" or "yesterday" if farther away than 24 hours
        if ($diff->format('%R%d') == 1) {
            return $view->translate('Tomorrow') . $time;
        } else if ($diff->format('%R%d') == -1) {
            return $view->translate('Yesterday') . $time;
        }

        // Print hours if farther away than 60 minutes
        if ($diff->format('%h') > 1) {
            if ($past) {
                return sprintf($view->translate('%s hours ago'), $diff->format('%h'));
            } else {
                return sprintf($view->translate('In %s hours'), $diff->format('%h'));
            }
        } else if ($diff->format('%h') == 1) {
            if ($past) {
                return $view->translate('One hour ago');
            } else {
                return $view->translate('In one hour');
            }
        }

        // Print minutes
        if ($diff->format('%i') > 1) {
            if ($past) {
                return sprintf($view->translate('%s minutes ago'), $diff->format('%i'));
            } else {
                return sprintf($view->translate('In %s minutes'), $diff->format('%i'));
            }
        } else if ($diff->format('%i') == 1) {
            if ($past) {
                return $view->translate('One minute ago');
            } else {
                return $view->translate('In one minute');
            }
        } else if ($diff->format('%i') == 0) {
            return $view->translate('Now');
        }

        return sprintf($view->translate('On %s'), $view->dateFormat($datetime, IntlDateFormatter::LONG) . $time);
    }

}