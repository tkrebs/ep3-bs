<?php

namespace Square\View\Helper;

use Base\Manager\OptionManager;
use DateTime;
use Square\Entity\Square;
use Square\Manager\SquarePricingManager;
use User\Manager\UserSessionManager;
use Zend\View\Helper\AbstractHelper;

class PricingSummary extends AbstractHelper
{

    protected $optionManager;
    protected $squarePricingManager;
    protected $user;

    public function __construct(OptionManager $optionManager,
        SquarePricingManager $squarePricingManager,
        UserSessionManager $userSessionManager)
    {
        $this->optionManager = $optionManager;
        $this->squarePricingManager = $squarePricingManager;
        $this->user = $userSessionManager->getSessionUser();
    }

    public function __invoke(DateTime $dateStart, DateTime $dateEnd, Square $square, $quantity = 1, array $products = array())
    {
        $pricingVisibility = $this->optionManager->get('service.pricing.visibility', 'private');

        if ($pricingVisibility == 'never') {
            return null;
        }

        $finalPricing = $this->squarePricingManager->getFinalPricingInRange($dateStart, $dateEnd, $square, $quantity);

        if (! $finalPricing) {
            return null;
        }

        $total = 0;

        $view = $this->getView();
        $html = '';

        $html .= '<table class="bordered-table middle-table">';
        $html .= '<tr>';

        $html .= sprintf('<td>' . $view->t('<b>%s %s</b><div class="small-text">%s</div>') . '</td>',
            $this->optionManager->need('subject.square.type'),
            $view->t($square->need('name')),
            $view->dateRange($dateStart, $dateEnd));

        if ($quantity == 1) {
            $squareUnit = $this->optionManager->need('subject.square.unit');
        } else {
            $squareUnit = $this->optionManager->need('subject.square.unit.plural');
        }

        $html .= sprintf('<td>%s</td>',
            $view->prettyTime($finalPricing['seconds']));

        $html .= sprintf('<td>%s %s</td>',
            $view->numberFormat($quantity), $squareUnit);

        $html .= sprintf('<td>%s</td>',
            $view->priceFormat($finalPricing['price'], $finalPricing['rate'], $finalPricing['gross']));

        $html .= '</tr>';

        $total += $finalPricing['price'];

        /* Render additional square products */

        foreach ($products as $product) {
            $html .= '<tr>';

            $productTotal = $product->need('price') * $product->needExtra('amount');

            $html .= '<td>' . $product->need('name') . '</td>';
            $html .= '<td colspan="2">' . sprintf($view->t('%s items'), $product->needExtra('amount')) . '</td>';
            $html .= '<td>' . $view->priceFormat($productTotal, $product->need('rate'), $product->need('gross')) . '</td>';

            $total += $productTotal;

            $html .= '</tr>';
        }

        /* Render total */

        $html .= '<td colspan="3" style="border-top: solid 2px #666;">'. $view->t('Total') . '</td>';
        $html .= '<td style="border-top: solid 2px #666;">' . $view->priceFormat($total) . '</td>';

        $html .= '</table>';

        return $html;
    }

}