<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class Event extends AbstractHelper
{

    public function __invoke($event, array $cellLinkParams)
    {
        $view = $this->getView();

        $cellLabel = $event->getMeta('name', '?');
        $cellUrl = $view->url('square', [], $cellLinkParams);
        $cellClass = 'cc-event cc-group-' . $event->need('eid');

        return $view->calendarCellLink($cellLabel, $cellUrl, $cellClass);
    }

}