<?php

namespace Base\Manager;

use RuntimeException;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class ConfigManager implements EventManagerAwareInterface
{

    protected $config;
    protected $events;

    protected $prepared = false;

    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(get_class($this));

        return $this->events = $events;
    }

    public function getEventManager()
    {
        if (! $this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    public function prepare()
    {
        if (! $this->prepared) {
            $this->getEventManager()->trigger('prepare', $this);
            $this->prepared = true;
        }
    }

    public function set($key, $value)
    {
        if ($this->prepared) {
            throw new RuntimeException('Cannot alter configuration after it has been prepared');
        }

        if (! (is_string($key) && strlen($key) > 0)) {
            throw new RuntimeException('Invalid config key to set');
        }

        $parts = explode('.', $key);
        $partsCount = count($parts);

        $tmpConfig = &$this->config;

        for ($i = 0; $i < $partsCount; $i++) {
            if ($i == $partsCount - 1) {
                $tmpConfig[$parts[$i]] = $value;
            } else {
                if (! isset($tmpConfig[$parts[$i]])) {
                    $tmpConfig[$parts[$i]] = array();
                }

                $tmpConfig = &$tmpConfig[$parts[$i]];
            }
        }
    }

    public function get($key, $default = null)
    {
        if (! (is_string($key) && strlen($key) > 0)) {
            if ($default === false) {
                throw new RuntimeException('Invalid config key requested');
            } else {
                return $default;
            }
        }

        $parts = explode('.', $key);
        $tmpConfig = &$this->config;

        foreach ($parts as $part) {
            if (isset($tmpConfig[$part])) {
                $tmpConfig = &$tmpConfig[$part];
            } else {
                if ($default === false) {
                    throw new RuntimeException(sprintf('Config key %s is missing', $key));
                } else {
                    return $default;
                }
            }
        }

        return $tmpConfig;
    }

    public function need($key)
    {
        return $this->get($key, false);
    }

}