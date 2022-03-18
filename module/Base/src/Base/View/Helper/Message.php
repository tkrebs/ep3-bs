<?php

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Message extends AbstractHelper
{

    public function __invoke($message, $type = 'success')
    {
        if ($message) {
            $view = $this->getView();

            return sprintf('<div class="%s-message message">%s</div>',
                $type, $view->translate($message));

        } else {
            return null;
        }
    }

}