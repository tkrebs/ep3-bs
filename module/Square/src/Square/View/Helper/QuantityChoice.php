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

        $askNames = $square->getMeta('capacity-ask-names');

        if ($askNames && $quantityAvailable > 1) {
            $askNamesSegments = explode('-', $askNames);

            $html .= '<div class="sb-player-names">';

            $html .= '<div class="separator separator-line"></div>';

            if (isset($askNamesSegments[0]) && $askNamesSegments[0] == 'optional') {
                $html .= sprintf('<p class="sb-player-names-mode gray" data-mode="optional">%s</p>',
                    $this->view->translate('The names of the other players are <b>optional</b>'));
            } else {
                $html .= sprintf('<p class="sb-player-names-mode gray" data-mode="required">%s</p>',
                    $this->view->translate('The names of the other players are <b>required</b>'));
            }

            for ($i = 2; $i <= $quantityAvailable; $i++) {
                $html .= sprintf('<div class="sb-player-name sb-player-name-%s" style="margin-bottom: 4px;">', $i);

                $html .= sprintf('<input type="text" name="sb-player-name-%1$s" id="sb-name-%1$s" value="" placeholder="%1$s. %2$s" style="min-width: 160px;">',
                    $i, $this->view->translate('Player\'s name'));

                if (isset($askNamesSegments[2]) && $askNamesSegments[2] == 'email') {

                    $html .= sprintf(' <input type="text" name="sb-player-email-%1$s" id="sb-player-email-%1$s" value="" placeholder="...%2$s" style="min-width: 160px;">',
                        $i, $this->view->translate('and email address'));
                }

                if ((isset($askNamesSegments[2]) && $askNamesSegments[2] == 'phone') ||
                    (isset($askNamesSegments[3]) && $askNamesSegments[3] == 'phone')) {

                    $html .= sprintf(' <input type="text" name="sb-player-phone-%1$s" id="sb-player-phone-%1$s" value="" placeholder="...%2$s" style="min-width: 160px;">',
                        $i, $this->view->translate('and phone number'));
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        }

        return $html;
    }

}
