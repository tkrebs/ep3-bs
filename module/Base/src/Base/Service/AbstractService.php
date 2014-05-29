<?php

namespace Base\Service;

use RuntimeException;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\TranslatorInterface;

abstract class AbstractService implements EventManagerAwareInterface
{

    protected $events;
    protected $translator;

    /**
     * Provides the service's event manager.
     *
     * @param EventManagerInterface $events
     *
     * @return EventManagerInterface
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(get_class($this));

        return $this->events = $events;
    }

    /**
     * Gets the service's event manager.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if ($this->events == null) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    /**
     * Provides the translator for this service.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Gets the translator for this service.
     *
     * @return TranslatorInterface
     * @throws RuntimeException
     */
    public function getTranslator()
    {
        if (! $this->translator) {
            throw new RuntimeException('Translator has not yet been injected');
        }

        return $this->translator;
    }

    /**
     * Translates messages with the service's translator.
     *
     * @param string $message
     * @return string
     */
    protected function translate($message)
    {
        return $this->getTranslator()->translate($message);
    }

    /**
     * Convenience method to translate messages with the service's translator.
     *
     * @param string $message
     * @return string
     */
    protected function t($message)
    {
        return $this->translate($message);
    }

}