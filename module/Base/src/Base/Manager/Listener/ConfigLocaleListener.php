<?php

namespace Base\Manager\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Stdlib\RequestInterface as Request;

class ConfigLocaleListener extends AbstractListenerAggregate
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function attach(EventManagerInterface $events)
    {
        $events->attach('prepare', array($this, 'onPrepare'));
    }

    public function onPrepare(Event $event)
    {
        if ($this->request instanceof HttpRequest) {

            $configManager = $event->getTarget();
            $configI18n = $configManager->need('i18n');

            $cookieNamePrefix = $configManager->need('cookie_config.cookie_name_prefix');
            $cookieName = $cookieNamePrefix . '-locale';

            $locale = $this->request->getQuery('locale');

            if ($locale && isset($configI18n['choice'][$locale])) {
                $configManager->set('i18n.locale', $locale);

                setcookie($cookieName, $locale, time() + 1209600, '/');
            } else {
                if (isset($_COOKIE[$cookieName])) {
                    $locale = $_COOKIE[$cookieName];

                    if (isset($configI18n['choice'][$locale])) {
                        $configManager->set('i18n.locale', $locale);
                    }
                } else {
                    $headers = $this->request->getHeaders();

                    if ($headers->has('Accept-Language')) {
                        $acceptedLocales = $headers->get('Accept-Language')->getPrioritized();

                        foreach ($acceptedLocales as $acceptedLocale) {
                            $acceptedLocaleParts = preg_split('/[\-\_]/', $acceptedLocale->getLanguage());
                            $acceptedLocalePart = $acceptedLocaleParts[0];

                            foreach ($configI18n['choice'] as $locale => $title) {
                                $localeParts = preg_split('/[\-\_]/', $locale);
                                $localePart = $localeParts[0];

                                if ($localePart == $acceptedLocalePart) {
                                    $configManager->set('i18n.locale', $locale);
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}