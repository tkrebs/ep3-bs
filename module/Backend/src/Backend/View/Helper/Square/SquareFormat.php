<?php

namespace Backend\View\Helper\Square;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class SquareFormat extends AbstractHelper
{

    public function __invoke(Square $square)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<tr>';

        $html .= sprintf('<td class="priority-col">%s</td>',
            $square->get('priority'));

        $html .= sprintf('<td><span class="gray">%s</span> %s</td>',
            $view->option('subject.square.type'),
            $square->get('name'));

        $html .= sprintf('<td>%s</td>',
            $view->t($square->getStatus()));

        $html .= sprintf('<td>%s</td>',
            $view->timeRange($square->need('time_start'), $square->need('time_end'), '%s to %s'));

        $html .= sprintf('<td>%s</td>',
            $view->prettyTime($square->need('time_block')));

        $html .= sprintf('<td>%s</td>',
            $view->prettyTime($square->need('time_block_bookable')));

        $html .= sprintf('<td>%s</td>',
            $view->prettyTime($square->need('time_block_bookable_max')));

        $html .= '<td class="actions-col no-print">'
            . '<a href="' . $view->url('backend/config/square/edit', ['sid' => $square->need('sid')]) . '" class="unlined gray symbolic symbolic-edit">' . $view->t('Edit') . '</a> &nbsp; '
            . '<a href="' . $view->url('backend/config/square/delete', ['sid' => $square->need('sid')]) . '" class="unlined gray symbolic symbolic-cross">' . $view->t('Delete') . '</a></td>';

        $html .= '</tr>';

        return $html;
    }

}