<?php

namespace Square\View\Helper;

use Base\Manager\OptionManager;
use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class QuantityChoice extends AbstractHelper
{

    protected $optionManager;

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    public function __invoke(Square $square, array $bookings)
    {
        $quantityAvailable = $square->need('capacity');

        foreach ($bookings as $booking) {
            $quantityAvailable -= $booking->need('quantity');
        }

        $view = $this->getView();
        $html = '';

        $html .= '<label for="sb-quantity" style="margin-right: 8px;">';
        $html .= sprintf($view->t('How many %s?'), $this->optionManager->need('subject.square.unit.plural'));
        $html .= '</label>';

        $html .= '<select id="sb-quantity" style="min-width: 64px;">';

        for ($i = 1; $i <= $quantityAvailable; $i++) {
            $html .= sprintf('<option value="%1$s">%1$s</option>', $i);
        }

        $html .= '</select>';

        $quantityOccupied = $square->need('capacity') - $quantityAvailable;

        $capacityInfo = $view->squareCapacityInfo($square, $quantityOccupied, 'span');

        if ($capacityInfo) {
            $html .= '<span style="margin-left: 8px;">' . $capacityInfo . '</span>';
        }

        return $html;
    }

}