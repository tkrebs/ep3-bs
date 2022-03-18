<?php

namespace Service\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ServiceController extends AbstractActionController
{

    public function infoAction()
    { }

    public function helpAction()
    { }

    public function statusAction()
    {
        if ($this->option('service.maintenance', 'false') == 'true') {
            $title = 'Maintenance';
            $status = 'maintenance';

            $response = $this->getResponse();
            $response->setStatusCode(503);
        } else {
            $title = 'System status';
            $status = 'default';
        }

        return array(
            'title' => $title,
            'status' => $status,
        );
    }

}