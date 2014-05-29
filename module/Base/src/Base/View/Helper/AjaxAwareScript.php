<?php

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;

class AjaxAwareScript extends AbstractHelper
{

    public function __invoke($script)
    {
        $view = $this->getView();

        if ($view->ajax) {
            $scriptFile = getcwd() . '/public/' . $script;

            if (is_readable($scriptFile)) {
                return '<script type="text/javascript">' . file_get_contents($scriptFile) . '</script>';
            }
        } else {
            $view->headScript()->appendFile($view->basePath($script));
        }
    }

}