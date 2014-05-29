<?php

namespace Base\View\Helper\Layout;

use Base\Manager\ConfigManager;
use Zend\Uri\Http as HttpUri;
use Zend\View\Helper\AbstractHelper;

class HeaderLocaleChoice extends AbstractHelper
{

    protected $configManager;
    protected $uri;

    public function __construct(ConfigManager $configManager, HttpUri $uri)
    {
        $this->configManager = $configManager;
        $this->uri = $uri;
    }

    public function __invoke()
    {
        $localeChoice = $this->configManager->get('i18n.choice');

        if (! ($localeChoice && is_array($localeChoice))) {
            return null;
        }

        $view = $this->getView();
        $html = '';

        $html .= '<div id="topbar-i18n">';

        foreach ($localeChoice as $locale => $title) {
            $uriString = $this->uri->toString();
            $localePattern = '/locale=[^&]+/';

            if (preg_match($localePattern, $uriString)) {
                $href = preg_replace($localePattern, 'locale=' . $locale, $uriString);
            } else {
                if ($this->uri->getQuery()) {
                    $href = $uriString . '&locale=' . $locale;
                } else {
                    $href = $uriString . '?locale=' . $locale;
                }
            }

            $html .= sprintf('<div><a href="%1$s" title="%2$s" class="unlined white"><img src="%3$s" alt="%2$s"></a></div>',
                $href, $title, $view->basePath('imgs/icons/locale/' . $locale . '.png'));
        }

        $html .= '</div>';

        return $html;
    }

}