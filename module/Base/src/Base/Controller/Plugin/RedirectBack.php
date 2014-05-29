<?php

namespace Base\Controller\Plugin;

use Base\Manager\ConfigManager;
use RuntimeException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Serializer\Adapter\Json as JsonSerializer;
use Zend\Serializer\Exception\ExceptionInterface;

class RedirectBack extends AbstractPlugin
{

    protected $serializer;

    protected $cookieName;
    protected $defaultOrigin;

    public function __construct(ConfigManager $configManager)
    {
        $this->serializer = new JsonSerializer();

        $this->cookieName = $configManager->need('redirect_config.cookie_name');
        $this->defaultOrigin = $configManager->need('redirect_config.default_origin');
    }

    public function toOrigin()
    {
        $controller = $this->getController();

        $origin = $this->getOrigin();

        if ($origin && is_array($origin) && isset($origin['route']) && isset($origin['params']) && isset($origin['options'])) {
            try {
                return $controller->redirect()->toRoute($origin['route'], $origin['params'], $origin['options']);
            } catch (RuntimeException $e) {
                return $controller->redirect()->toRoute($this->defaultOrigin);
            }
        } else {
            return $controller->redirect()->toRoute($this->defaultOrigin);
        }
    }

    public function setOrigin($route, array $params = array(), array $options = array())
    {
        $origin = $this->serializer->serialize(array(
            'route' => $route,
            'params' => $params,
            'options' => $options,
        ));

        setcookie($this->cookieName, $origin, 0, '/');
    }

    public function getOrigin()
    {
        if (isset($_COOKIE[$this->cookieName])) {
            try {
                return $this->serializer->unserialize($_COOKIE[$this->cookieName]);
            } catch (ExceptionInterface $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getOriginAsUrl()
    {
        $controller = $this->getController();

        $origin = $this->getOrigin();

        if ($origin && is_array($origin) && isset($origin['route']) && isset($origin['params']) && isset($origin['options'])) {
            return $controller->url()->fromRoute($origin['route'], $origin['params'], $origin['options']);
        } else {
            return null;
        }
    }

}