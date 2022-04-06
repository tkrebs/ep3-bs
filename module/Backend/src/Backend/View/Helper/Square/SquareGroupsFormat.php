<?php

namespace Backend\View\Helper\Square;

use Zend\View\Helper\AbstractHelper;

class SquareGroupsFormat extends AbstractHelper
{

    public function __invoke(array $squareGroups)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<table class="bordered-table">';

        $html .= '<tr class="gray">';
        $html .= '<th>&nbsp;</th>';
        $html .= '<th>' . $view->t('Description') . '</th>';
        $html .= '<th class="no-print">&nbsp;</th>';
        $html .= '</tr>';

        foreach ($squareGroups as $squareGroup) {
            $html .= $view->backendSquareGroupFormat($squareGroup);
        }

        $html .= '</table>';

        $html .= '<style type="text/css"> .priority-col, .actions-col { border: none !important; } </style>';

        $view->headScript()->appendFile($view->basePath('js/controller/backend/config-square/products.min.js'));

        return $html;
    }

}