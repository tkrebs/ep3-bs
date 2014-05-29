<?php

namespace Backend\View\Helper\Square;

use Zend\View\Helper\AbstractHelper;

class ProductsFormat extends AbstractHelper
{

    public function __invoke(array $squareProducts)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<table class="bordered-table">';

        $html .= '<tr class="gray">';
        $html .= '<th>&nbsp;</th>';
        $html .= '<th>' . $view->t('Name') . '</th>';
        $html .= '<th>' . $view->option('subject.square.type') . '</th>';
        $html .= '<th>' . $view->t('Price') . '</th>';
        $html .= '<th class="no-print">&nbsp;</th>';
        $html .= '</tr>';

        foreach ($squareProducts as $squareProduct) {
            $html .= $view->backendSquareProductFormat($squareProduct);
        }

        $html .= '</table>';

        $html .= '<style type="text/css"> .priority-col, .actions-col { border: none !important; } </style>';

        $view->headScript()->appendFile($view->basePath('js/controller/backend/config-square/products.min.js'));

        return $html;
    }

}