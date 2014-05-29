<?php

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class Messages extends AbstractHelper
{

    protected $flashMessenger;

    public function setFlashMessenger(FlashMessenger $flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
    }

    public function __invoke()
    {
        $view = $this->getView();
        $html = '';

        // Print all placeholder messages
        $placeholderMessages = $view->placeholder('messages')->getValue();

        if (is_array($placeholderMessages)) {
            foreach ($placeholderMessages as $type => $message) {
                if ($type && $message) {
                    if (is_array($message)) {
                        foreach ($message as $partMessage) {
                            $html .= $view->message($partMessage, $type);
                        }
                    } else {
                        $html .= $view->message($message, $type);
                    }
                }
            }
        }

        // Print all flash messages, if flash messenger is available
        if ($this->flashMessenger) {
            $flashMessageTypes = array(
                FlashMessenger::NAMESPACE_DEFAULT,
                FlashMessenger::NAMESPACE_SUCCESS,
                FlashMessenger::NAMESPACE_INFO,
                FlashMessenger::NAMESPACE_ERROR,
            );

            foreach ($flashMessageTypes as $type) {
                $flashMessages = $this->flashMessenger->getMessagesFromNamespace($type);

                foreach ($flashMessages as $message) {
                    $html .= $view->message($message, $type);
                }
            }
        }

        if ($html) {
            $html = sprintf('<div class="%s messages-panel panel">%s</div>',
                str_replace('phantom-panel', '', $view->placeholder('panel')), $html);
        }

        return $html;
    }

}