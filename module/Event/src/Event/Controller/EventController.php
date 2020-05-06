<?php

namespace Event\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class EventController extends AbstractActionController
{

    public function indexAction()
    {
        $serviceManager = @$this->getServiceLocator();
        $eventManager = $serviceManager->get('Event\Manager\EventManager');
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');

        $eid = $this->params()->fromRoute('eid');

        $event = $eventManager->get($eid);

        $eventManager->getSecondsPerDay($event);

        if ($event->get('sid')) {
            $square = $squareManager->get($event->need('sid'));
        } else {
            $square = null;
        }

        return $this->ajaxViewModel(array(
            'event' => $event,
            'square' => $square,
        ));
    }

}
