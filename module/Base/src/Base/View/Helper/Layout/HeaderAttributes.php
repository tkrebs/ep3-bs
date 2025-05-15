<?php

namespace Base\View\Helper\Layout;

use Zend\View\Helper\AbstractHelper;

class HeaderAttributes extends AbstractHelper
{

    public function __invoke()
    {
        $view = $this->getView();

        $misc = $view->placeholder('misc')->getValue();

        if (is_array($misc)) {
            if (isset($misc['header'])) {
                if (! $misc['header']) {
                    return 'style="display: none;"';
                }
            }
        }

        return null;
    }

}