<?php

namespace Base\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;

class DefaultViewModel extends AbstractPlugin
{

    public function __invoke($variables = null, $options = null, $template = null)
    {
        $viewModel = new ViewModel($variables, $options);

        if ($template) {
            $viewModel->setTemplate($template);
        }

        return $viewModel;
    }

}