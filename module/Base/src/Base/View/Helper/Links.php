<?php

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Links extends AbstractHelper
{

    public function __invoke()
    {
        $view = $this->getView();
        $html = '';

        $backHref = $view->placeholder('back-href')->getValue();
        $backTitle = $view->placeholder('back-title')->getValue();

        if ($backHref && $backTitle) {
            $html .= sprintf('<div class="links-back left-text"><a href="%s" class="unlined white back-button"><span class="light-gray">%s:</span><br>%s</a></div>',
                $backHref, $view->translate('Back to'), $backTitle);
        }

        $links = $view->placeholder('links')->getValue();

        if ($links) {
            $html .= '<div class="links-forth left-text no-wrap">';
            $html .= '<div class="light-gray">' . $view->translate('Related pages') . ':</div>';
            $html .= '<ul>';

            foreach ($links as $title => $href) {
                $html .= sprintf('<li><a href="%s" class="unlined white">%s</a></li>',
                    $href, $view->translate($title));
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        if ($html) {
            $html = '<div class="links centered-text no-print">' . $html . '</div>';
        }

        return $html;
    }

}