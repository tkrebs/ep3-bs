<?php

namespace Backend\View\Helper\Square;

use Zend\View\Helper\AbstractHelper;

class SquaresFormat extends AbstractHelper
{

    public function __invoke(array $squares)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<table class="bordered-table">';

        $html .= '<tr class="gray">';
        $html .= '<th>&nbsp;</th>';
        $html .= '<th>' . $view->t('Name') . '</th>';
        $html .= '<th>' . $view->t('Status') . '</th>';
        $html .= '<th>' . $view->t('Time') . '</th>';
        $html .= '<th>' . $view->t('Time block') . '</th>';
        $html .= '<th>' . $view->t('Time block (min. bookable)') . '</th>';
        $html .= '<th>' . $view->t('Time block (max. bookable)') . '</th>';
        $html .= '<th class="no-print">&nbsp;</th>';
        $html .= '</tr>';

        foreach ($squares as $square) {
            $html .= $view->backendSquareFormat($square);
        }

        $html .= '</table>';

        $html .= '<style type="text/css"> .priority-col, .actions-col { border: none !important; } </style>';

        $view->headScript()->appendFile($view->basePath('js/controller/backend/config-square/index.min.js'));

        return $html;
    }

}