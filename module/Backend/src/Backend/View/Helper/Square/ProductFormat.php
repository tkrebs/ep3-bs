<?php

namespace Backend\View\Helper\Square;

use Square\Entity\SquareProduct;
use Square\Manager\SquareManager;
use Zend\View\Helper\AbstractHelper;

class ProductFormat extends AbstractHelper
{

    protected $squareManager;

    public function __construct(SquareManager $squareManager)
    {
        $this->squareManager = $squareManager;
    }

    public function __invoke(SquareProduct $squareProduct)
    {
        $view = $this->getView();
        $html = '';

        $spid = $squareProduct->need('spid');

        $html .= '<tr>';

        $html .= sprintf('<td class="priority-col">%s</td>',
            $squareProduct->get('priority'));

        $html .= sprintf('<td>%s</td>',
            $squareProduct->get('name'));

        $sid = $squareProduct->get('sid');

        if ($sid) {
            $square = $this->squareManager->get($sid);
            $squareName = $square->get('name');
        } else {
            $squareName = sprintf($view->t('All %s'), $view->option('subject.square.type.plural'));
        }

        $html .= sprintf('<td>%s</td>',
            $squareName);

        $html .= sprintf('<td>%s</td>',
            $view->priceFormat($squareProduct->get('price'), $squareProduct->get('rate'), $squareProduct->get('gross'), null, null, 'per item'));

        /* Actions col */

        $html .= '<td class="actions-col no-print">'
            . '<a href="' . $view->url('backend/config/square/product/edit', ['spid' => $spid]) . '" class="unlined gray symbolic symbolic-config">' . $view->t('Edit') . '</a> &nbsp; '
            . '<a href="' . $view->url('backend/config/square/product/delete', ['spid' => $spid]) . '" class="unlined gray symbolic symbolic-cross">' . $view->t('Delete') . '</a></td>';

        $html .= '</tr>';

        return $html;
    }

}