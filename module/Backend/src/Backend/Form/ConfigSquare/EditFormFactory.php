<?php

namespace Backend\Form\ConfigSquare;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EditForm(
            $sm->getServiceLocator()->get('Square\Manager\SquareGroupManager'));
    }

}
