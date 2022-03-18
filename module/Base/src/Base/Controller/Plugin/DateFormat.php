<?php

namespace Base\Controller\Plugin;

use DateTime;
use IntlDateFormatter;
use Zend\I18n\View\Helper\DateFormat as DateFormatHelper;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DateFormat extends AbstractPlugin
{

    protected $dateFormatHelper;

    public function __construct(DateFormatHelper $dateFormatHelper)
    {
        $this->dateFormatHelper = $dateFormatHelper;
    }

    public function __invoke($dateTime, $dateType = IntlDateFormatter::MEDIUM, $timeType = IntlDateFormatter::NONE)
    {
        if (! $dateTime) {
            return null;
        }
        
        if (! ($dateTime instanceof DateTime)) {
            $dateTime = new DateTime($dateTime);
        }

        $dateFormatHelper = $this->dateFormatHelper;

        return $dateFormatHelper($dateTime, $dateType, $timeType);
    }

}