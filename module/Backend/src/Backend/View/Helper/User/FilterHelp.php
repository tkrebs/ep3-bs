<?php

namespace Backend\View\Helper\User;

use Zend\View\Helper\AbstractHelper;

class FilterHelp extends AbstractHelper
{

    public function __invoke($key, $operator, $value)
    {
        $view = $this->getView();

        if ($value instanceof \DateTime) {
            $value = $view->dateFormat($value, \IntlDateFormatter::MEDIUM);
        }

        return sprintf('<div><a href="#" class="unlined gray usf-filter-snippet"><code>(%s %s %s)</code></a></div>',
            strtolower($view->t($key)),
            $operator,
            mb_strtolower($view->t($value)));
    }

}
