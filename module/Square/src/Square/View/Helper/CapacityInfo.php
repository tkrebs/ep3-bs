<?php

namespace Square\View\Helper;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class CapacityInfo extends AbstractHelper
{

    public function __invoke(Square $square, $quantity, $wrap = 'p')
    {
        $squareCapacity = $square->need('capacity');
        $squareCapacityInfo = $square->getMeta('info.capacity');

        if (! $squareCapacityInfo) {
            if ($squareCapacity == 1) {
                $squareCapacityInfo = 'false';
            } else {
                $squareCapacityInfo = 'true';
            }
        }

        if ($squareCapacityInfo == 'true') {
            if ($quantity > 0) {
                return sprintf('<%s><span class="yellow">' . $this->getView()->t('%s/%s already occupied') . '</span></%s>',
                    $wrap, $quantity, $squareCapacity, $wrap);
            }
        }

        return null;
    }

}