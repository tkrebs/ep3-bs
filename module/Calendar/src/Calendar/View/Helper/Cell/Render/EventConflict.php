<?php

namespace Calendar\View\Helper\Cell\Render;

use Zend\View\Helper\AbstractHelper;

class EventConflict extends AbstractHelper
{

    public function __invoke($user, array $events, array $reservations, array $cellLinkParams)
    {
        $view = $this->getView();

        if ($user && $user->can('calendar.see-data')) {
            if ($events && $reservations) {
                return $view->calendarCellLink('Conflict', $view->url('backend/booking/edit', [], $cellLinkParams), 'cc-conflict');
            }

            if (count($events) > 1) {
                return $view->calendarCellLink('Conflict', $view->url('backend/event/edit-choice', [], $cellLinkParams), 'cc-conflict');
            }
        }

        $event = current($events);
        return $view->calendarCellRenderEvent($user, $event, $cellLinkParams);
    }

}