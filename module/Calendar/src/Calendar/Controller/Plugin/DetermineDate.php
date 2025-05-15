<?php

namespace Calendar\Controller\Plugin;

use DateTime;
use Exception;
use RuntimeException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DetermineDate extends AbstractPlugin
{

    public function __invoke()
    {
        $controller = $this->getController();

        try {
            $passedDate = $controller->params()->fromQuery('date');

            if (! $passedDate) {
                if ($controller->cookie()->get('calendar-date')) {
                    $passedDate = $controller->cookie()->get('calendar-date');
                } else {
                    $passedDate = 'now';
                }
            }

            $dateStart = new DateTime($passedDate);
            $dateStart->setTime(0, 0);

            if ($dateStart) {
                $controller->cookie()->set('calendar-date', $dateStart->format('Y-m-d'));
            }

            return $dateStart;
        } catch (Exception $e) {
            throw new RuntimeException('The passed calendar date is invalid');
        }
    }

}