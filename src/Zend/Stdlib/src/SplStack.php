<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use ReturnTypeWillChange;
use Serializable;

/**
 * Serializable version of SplStack
 */
class SplStack extends \SplStack implements Serializable
{
    /**
     * Serialize to an array representing the stack
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Serialize
     *
     * @return string
     */
    #[ReturnTypeWillChange] public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserialize
     *
     * @param  string $data
     * @return void
     */
    #[ReturnTypeWillChange] public function unserialize($data)
    {
        foreach (unserialize($data) as $item) {
            $this->unshift($item);
        }
    }
}
