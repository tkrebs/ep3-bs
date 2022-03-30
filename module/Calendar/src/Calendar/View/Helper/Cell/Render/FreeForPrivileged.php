<?php

namespace Calendar\View\Helper\Cell\Render;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;
use Square\Manager\SquarePricingManager;

class FreeForPrivileged extends AbstractHelper
{

    public function __invoke(array $reservations, array $cellLinkParams, Square $square)
    {
        $view = $this->getView();

        $reservationsCount = count($reservations);

        $serviceManager = $view->getHelperPluginManager()->getServiceLocator();
        
        $squarePricingManager = $serviceManager->get('Square\Manager\SquarePricingManager');
        $dateTimeStart = new \DateTime($cellLinkParams["query"]["ds"] . ' ' . $cellLinkParams["query"]["ts"]);
        $rangeTimeStart = new \DateTime($cellLinkParams["query"]["ds"] . ' ' . "0:00");
        $dateTimeEnd = new \DateTime($cellLinkParams["query"]["ds"] . ' ' . $cellLinkParams["query"]["te"]);
        $rangeTimeEnd = new \DateTime($cellLinkParams["query"]["ds"] . ' ' . "23:00");
        $pricing = $squarePricingManager->
            getFinalPricingInRange($dateTimeStart, $dateTimeEnd, $square, 1);
        $minMaxPrice = $squarePricingManager->
            getMinMaxPricingInRange($rangeTimeStart, $rangeTimeEnd, $square, 1);
        $minPrice = $minMaxPrice["minPrice"];
        $maxPrice = $minMaxPrice["maxPrice"];
        $priceClass = "cc-free";

        if ($reservationsCount == 0) {
	        $labelFree = $square->getMeta('label.free', $this->view->t('Free'));
            if ($pricing) {
                $price = $pricing["price"];
                $labelFree = $view->currencyFormat($price / 100);
                if ($price == $minPrice) {
                    $priceClass = "cc-free-min";
                }
                if ($price == $maxPrice) {
                    $priceClass = "cc-free-max";
                }
            } else {
                $priceClass = "cc-free-hidden";
            }

            return $view->calendarCellLink($labelFree, $view->url('backend/booking/edit', [], $cellLinkParams), $priceClass);
        } else if ($reservationsCount == 1) {
            $reservation = current($reservations);
            $booking = $reservation->needExtra('booking');

            $cellLabel = $booking->needExtra('user')->need('alias');
            $cellGroup = ' cc-group-' . $booking->need('bid');

            return $view->calendarCellLink($cellLabel, $view->url('backend/booking/edit', [], $cellLinkParams), $priceClass . ' ' . $priceClass . '-partially' . $cellGroup);
        } else {
	        $labelFree = $square->getMeta('label.free', 'Still free');

            return $view->calendarCellLink($labelFree, $view->url('backend/booking/edit', [], $cellLinkParams), $priceClass . ' ' . $priceClass . '-partially');
        }
    }

}
