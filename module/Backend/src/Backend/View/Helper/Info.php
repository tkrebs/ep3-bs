<?php

namespace Backend\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Info extends AbstractHelper
{

    public function __invoke($key)
    {
        $view = $this->getView();
        $html = '';

        switch ($key) {
            case 'i18n':
                $html .= $view->t('To provide language dependent content here, simply switch the global system language.');
                break;
        }

        $html = sprintf('<p class="symbolic symbolic-info">%s</p>', $html);

        return $html;
    }

}