<?php
/**
 * @see       https://github.com/zendframework/zend-mvc for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-mvc/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Mvc\Controller;

class PluginManager extends AbstractPluginManager
{
    /**
     * Retrieve a registered instance
     *
     * After the plugin is retrieved from the service locator, inject the
     * controller in the plugin every time it is requested. This is required
     * because a controller can use a plugin and another controller can be
     * dispatched afterwards. If this second controller uses the same plugin
     * as the first controller, the reference to the controller inside the
     * plugin is lost.
     *
     * @param string $name
     * @param null|array $options
     *
     * @return object
     */
    public function get($name, $options = [], $usePeeringServiceManagers = true)
    {
        $plugin = parent::get($name, $options, $usePeeringServiceManagers);
        $this->injectController($plugin);

        return $plugin;
    }
}
