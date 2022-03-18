<?php

namespace Base\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;

class AjaxViewModel extends AbstractPlugin
{

    public function __invoke($variables = null, $options = null, $template = null)
    {
        $ajax = $this->getController()->params()->fromQuery('ajax');

        if ($ajax == 'true') {
            if (! is_array($variables)) {
                $variables = array();
            }

            $variables = array_merge(array('ajax' => $ajax), $variables);
        }

        $viewModel = new ViewModel($variables, $options);

        if ($ajax == 'true') {
            $viewModel->setTerminal(true);
        }

        if ($template) {
            $viewModel->setTemplate($template);
        }

        return $viewModel;
    }

}