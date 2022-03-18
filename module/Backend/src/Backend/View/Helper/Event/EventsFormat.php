<?php

namespace Backend\View\Helper\Event;

use Zend\View\Helper\AbstractHelper;

class EventsFormat extends AbstractHelper
{

    public function __invoke(array $events, $dateStart = null, $dateEnd = null)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<table class="bordered-table">';

        $html .= '<tr class="gray">';
        $html .= '<th>' . $view->t('No.') . '</th>';
        $html .= '<th>' . $view->t('Name') . '</th>';
        $html .= '<th>' . $view->t('Start date') . '</th>';
        $html .= '<th>' . $view->t('End date') . '</th>';
        $html .= '<th>' . $view->t('Capacity') . '</th>';
        $html .= '<th>' . $view->option('subject.square.type') . '</th>';
        $html .= '<th class="no-print">&nbsp;</th>';
        $html .= '</tr>';

        foreach ($events as $event) {
            $html .= $view->backendEventFormat($event, $dateStart, $dateEnd);
        }

        $html .= '</table>';

        $html .= '<style type="text/css"> .actions-col { border: none !important; } </style>';

        $view->headScript()->appendFile($view->basePath('js/controller/backend/event/index.min.js'));

        return $html;
    }

}