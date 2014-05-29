<?php

namespace Base\View\Helper;

use NumberFormatter;
use Zend\View\Helper\AbstractHelper;

class PrettyTime extends AbstractHelper
{

    public function __invoke($time)
    {
        $view = $this->getView();

        $unit = $this->getUnit($time, 'Second', 'Seconds');

        if ($time > 180) {
            $time /= 60;
            $unit = $this->getUnit($time, 'Minute', 'Minutes');

            if ($time > 180) {
                $time /= 60;
                $unit = $this->getUnit($time, 'Hour', 'Hours');

                if ($time > 48) {
                    $time /= 24;
                    $unit = $this->getUnit($time, 'Day', 'Days');
                }
            }
        }

        return $view->numberFormat(round($time), NumberFormatter::DECIMAL, NumberFormatter::TYPE_DEFAULT) . ' ' . $unit;
    }

    protected function getUnit($number, $singular, $plural)
    {
        if ($number == 1) {
            return $this->getView()->translate($singular);
        } else {
            return $this->getView()->translate($plural);
        }
    }

}