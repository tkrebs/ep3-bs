<?php

namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class EventController extends AbstractActionController
{

    public function indexAction()
    {
        $this->authorize('admin.event');
    }

    public function statsAction()
    {
        $this->authorize('admin.event');
    }

}