<?php

namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $group = $this->getRequest()->getQuery('group-select');

        $calendarViewModel = $this->forward()->dispatch('Calendar\Controller\Calendar', ['action' => 'index']);
        $calendarViewModel->setCaptureTo('calendar');

        $group = $this->getRequest()->getQuery('group-select');
       

        $dateStart = $calendarViewModel->getVariable('dateStart');
        $dateNow = $calendarViewModel->getVariable('dateNow');
        $squaresFilter = $calendarViewModel->getVariable('squaresFilter');
        $user = $calendarViewModel->getVariable('user');
        $squareGroup = $calendarViewModel->getVariable('group-select');
        $group = $this->getRequest()->getQuery('group-select');

        $this->redirectBack()->setOrigin('frontend');

        $viewModel = new ViewModel(array(
            'dateStart' => $dateStart,
            'dateNow' => $dateNow,
            'squaresFilter' => $squaresFilter,
            'user' => $user,
            'groupSelect' => $group,
        ));

        $viewModel->addChild($calendarViewModel);

        return $viewModel;
    }

}
