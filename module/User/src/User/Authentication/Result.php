<?php

namespace User\Authentication;

use InvalidArgumentException;
use Zend\Authentication\Result as ZendResult;

class Result extends ZendResult
{

    /**
     * Failure due to too many tries.
     */
    const FAILURE_TOO_MANY_TRIES = -5;

    /**
     * Failure due to user status.
     */
    const FAILURE_USER_STATUS = -6;

    /**
     * Extra data to pass back.
     *
     * @var array
     */
    protected $extra = array();

    /**
     * Sets an extra key/value pair for this result.
     *
     * @param string $key
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function setExtra($key, $value)
    {
        if (! (is_string($key) || is_numeric($key))) {
            throw new InvalidArgumentException('Invalid parameter type for result extra key');
        }

        $this->extra[$key] = $value;
    }

    /**
     * Gets an extra value from this result.
     *
     * @param string $key
     * @return mixed
     */
    public function getExtra($key)
    {
        return $this->extra[$key] ?? null;
    }

}
