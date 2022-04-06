<?php

namespace Backend\Form\ConfigSquare;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditSquareGroupFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EditSquareGroupForm(
            $sm->getServiceLocator()->get('Base\Manager\ConfigManager'));
    }

}