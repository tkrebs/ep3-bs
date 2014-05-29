<?php

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Tabs extends AbstractHelper
{

    protected $currentRequestPath;

    public function __construct($currentRequestPath)
    {
        $this->currentRequestPath = $currentRequestPath;
    }

    public function __invoke()
    {
        $view = $this->getView();
        $html = '';

        $tabs = $view->placeholder('tabs')->getValue();
        $misc = $view->placeholder('misc')->getValue();

        if (is_array($tabs)) {
            foreach ($tabs as $tabTitle => $tabConfig) {
                $tabHtml = null;
                $tabHref = null;
                $tabOuterClass = null;
                $tabLinkClass = null;
                $tabInnerClass = null;

                if (is_array($tabConfig)) {
                    if (isset($tabConfig['html'])) {
                        $tabHtml = $tabConfig['html'];
                    }

                    if (isset($tabConfig['url'])) {
                        $tabHref = $tabConfig['url'];
                    }

                    if (isset($tabConfig['outer-class'])) {
                        $tabOuterClass = $tabConfig['outer-class'];
                    }

                    if (isset($tabConfig['link-class'])) {
                        $tabLinkClass = $tabConfig['link-class'];
                    }

                    if (isset($tabConfig['inner-class'])) {
                        $tabInnerClass = $tabConfig['inner-class'];
                    }
                } else if (is_string($tabConfig)) {
                    $tabHref = $tabConfig;
                }

                if ($tabHref) {
                    if (is_array($misc) && isset($misc['tabsActive'])) {
                        $tabsActive = $misc['tabsActive'];

                        if (is_string($tabsActive)) {
                            $tabsActive = array($tabsActive);
                        }

                        if (in_array($tabTitle, $tabsActive)) {
                            $tabLinkClass .= ' tab-active';
                        }
                    }

                    if ($tabHref == $this->currentRequestPath) {
                        $tabLinkClass .= ' tab-active';
                    }

                    $tabHtml .= sprintf('<a href="%s" class="unlined %s"><span class="inner-tab %s">%s</span></a>',
                        $tabHref, $tabLinkClass, $tabInnerClass, $view->translate($tabTitle));
                }

                if ($tabHtml) {
                    $html .= sprintf('<span class="outer-tab %s">%s</span>',
                        $tabOuterClass, $tabHtml);
                }
            }
        }

        if ($html) {
            $html = sprintf('<div class="%s phantom-panel tabs-panel">%s</div>',
                $view->placeholder('panel'), $html);
        }

        return $html;
    }

}