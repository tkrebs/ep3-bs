<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class EventForPrivileged extends AbstractHelper
{

    public function __invoke($event)
    {
        $view = $this->getView();

        $eid = $event->need('eid');

        $cellLabel = $event->getMeta('name', '?');
        $cellUrl = $view->url('backend/event/edit', ['eid' => $eid]);
        $cellClass = 'cc-event cc-group-' . $eid . ' squarebox-external-link';

        return $view->calendarCellLink($cellLabel, $cellUrl, $cellClass);
    }

}