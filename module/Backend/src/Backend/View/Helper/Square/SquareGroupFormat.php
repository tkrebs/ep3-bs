<?php

namespace Backend\View\Helper\Square;

use Square\Entity\SquareGroup;
use Square\Manager\SquareManager;
use Zend\View\Helper\AbstractHelper;

class SquareGroupFormat extends AbstractHelper
{

    protected $squareManager;

    public function __construct(SquareManager $squareManager)
    {
        $this->squareManager = $squareManager;
    }

    public function __invoke(SquareGroup $squareGroup)
    {
        $view = $this->getView();
        $html = '';

        $sgid = $squareGroup->need('sgid');

        $html .= '<tr>';

        $html .= sprintf('<td class="priority-col">%s</td>',
            $squareGroup->get('sgid'));

        $html .= sprintf('<td>%s</td>',
            $squareGroup->get('description'));

        /* Actions col */

        $html .= '<td class="actions-col no-print">'
            . '<a href="' . $view->url('backend/config/square/squaregroup/edit', ['sgid' => $sgid]) . '" class="unlined gray symbolic symbolic-config">' . $view->t('Edit') . '</a> &nbsp; '
            . '<a href="' . $view->url('backend/config/square/squaregroup/delete', ['sgid' => $sgid]) . '" class="unlined gray symbolic symbolic-cross">' . $view->t('Delete') . '</a></td>';

        $html .= '</tr>';

        return $html;
    }

}