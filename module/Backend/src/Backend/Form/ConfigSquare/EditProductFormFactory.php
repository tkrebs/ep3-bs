<?php

namespace Backend\Form\ConfigSquare;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditProductFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sm)
    {
        return new EditProductForm(
            $sm->getServiceLocator()->get('Base\Manager\ConfigManager'),
            $sm->getServiceLocator()->get('Square\Manager\SquareManager'));
    }

}