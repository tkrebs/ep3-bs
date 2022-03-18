<?php

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Setup extends AbstractHelper
{

    public function __invoke(array $config = array())
    {
        $view = $this->getView();

        if (isset($config['title']) && is_string($config['title'])) {
            $view->headTitle($view->translate($config['title']));
        }

        if (isset($config['panel']) && is_string($config['panel'])) {
            $view->placeholder('panel')->set($config['panel']);
        }

        if (isset($config['messages']) && is_array($config['messages'])) {
            $view->placeholder('messages')->set($config['messages']);
        }

        if (isset($config['back'])) {
            if (is_array($config['back'])) {
                $href = current($config['back']);
                $title = key($config['back']);

                $this->getView()->placeholder('back-href')->set($href);
                $this->getView()->placeholder('back-title')->set($view->translate($title));
            } else {
                $this->getView()->placeholder('back-href')->set($view->basePath('/'));
                $this->getView()->placeholder('back-title')->set($view->translate('Calendar'));
            }
        }

        if (isset($config['links']) && is_array($config['links'])) {
            $view->placeholder('links')->set($config['links']);
        }

        if (isset($config['tabs']) && is_array($config['tabs'])) {
            $view->placeholder('tabs')->set($config['tabs']);
        }

        if (isset($config['misc']) && is_array($config['misc'])) {
            $view->placeholder('misc')->set($config['misc']);
        }
    }

}