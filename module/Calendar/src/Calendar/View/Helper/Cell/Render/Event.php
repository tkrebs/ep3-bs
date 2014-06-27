<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class Event extends AbstractHelper
{

    public function __invoke($user, $event, array $cellLinkParams)
    {
        $view = $this->getView();

        if ($user && $user->can('admin.events')) {
            return $view->calendarCellRenderEventForPrivileged($event);
        } else {
            $eid = $event->need('eid');

            $cellLabel = $event->getMeta('name', '?');
            $cellUrl = $view->url('event', ['eid' => $eid]);
            $cellClass = 'cc-event cc-group-' . $eid;

            return $view->calendarCellLink($cellLabel, $cellUrl, $cellClass);
        }
    }

}