<?php

namespace Backend\View\Helper\Event;

use Event\Entity\Event;
use Square\Manager\SquareManager;
use Zend\View\Helper\AbstractHelper;

class EventFormat extends AbstractHelper
{

    protected $squareManager;

    public function __construct(SquareManager $squareManager)
    {
        $this->squareManager = $squareManager;
    }

    public function __invoke(Event $event, $dateStart = null, $dateEnd = null)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<tr>';

        $html .= sprintf('<td>%s</td>',
            $event->need('eid'));

        /* Name col */

        $name = $event->getMeta('name', '?');

        if (strlen($name) > 48) {
            $name = substr($name, 0, 48) . '&hellip;';
        }

        $html .= sprintf('<td>%s</td>',
            $name);

        /* Date cols */

        $html .= sprintf('<td>%s</td>',
            $view->dateFormat($event->needExtra('datetime_start'), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT));

        $html .= sprintf('<td>%s</td>',
            $view->dateFormat($event->needExtra('datetime_end'), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT));

        /* Capacity col */

        $capacity = $event->get('capacity');

        if ($capacity) {
            $capacityLabel = $capacity;
        } else {
            $capacityLabel = $view->t('None');
        }

        $html .= sprintf('<td>%s</td>',
            $capacityLabel);

        /* Square col */

        if ($event->get('sid')) {
            $squareLabel = $this->squareManager->get($event->need('sid'))->get('name');
        } else {
            $squareLabel = $view->t('All');
        }

        $html .= sprintf('<td>%s</td>',
            $squareLabel);

        /* Actions col */

        $html .= sprintf('<td class="actions-col no-print"><a href="%s" class="unlined gray symbolic symbolic-edit">%s</a></td>',
            $view->url('backend/event/edit', ['eid' => $event->need('eid')]),
            $view->t('Edit'));

        $html .= '</tr>';

        return $html;
    }

}