<?php

namespace Base\Manager;

use RuntimeException;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\TranslatorInterface;

abstract class AbstractManager implements EventManagerAwareInterface
{

    protected $events;
    protected $translator;

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

    /**
     * Provides the translator for this manager.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Gets the translator for this manager.
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
     * Translates messages with the manager's translator.
     *
     * @param string $message
     * @return string
     */
    protected function translate($message)
    {
        return $this->getTranslator()->translate($message);
    }

    /**
     * Convenience method to translate messages with the manager's translator.
     *
     * @param string $message
     * @return string
     */
    protected function t($message)
    {
        return $this->translate($message);
    }

}