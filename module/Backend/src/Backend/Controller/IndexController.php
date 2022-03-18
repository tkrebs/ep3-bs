<?php

namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $this->authorize('admin.see-menu');

        return $this->ajaxViewModel();
    }

}