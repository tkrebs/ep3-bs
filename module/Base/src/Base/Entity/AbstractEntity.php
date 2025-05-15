<?php

namespace Base\Entity;

use InvalidArgumentException;
use RuntimeException;

/**
 * Entity objects are containers for properties, meta data and extra data.
 * They keep track of which properties and meta data have been updated or removed.
 */
abstract class AbstractEntity
{

    protected $primary = null;

    protected $meta = array();
    protected $extra = array();

    protected $updatedProperties = array();
    protected $updatedMetaProperties = array();
    protected $insertedMetaProperties = array();
    protected $removedMetaProperties = array();

    /**
     * Creates a new entity object.
     *
     * @param array $properties             Must be an array with scalar key/value pairs.
     * @param array $metaProperties         Must be an array with scalar key/value pairs.
     */
    public function __construct(array $properties = array(), array $metaProperties = array())
    {
        foreach ($properties as $key => $value) {
            $this->add($key, $value);
        }

        foreach ($metaProperties as $key => $value) {
            $this->addMeta($key, $value);
        }
    }

    /**
     * Adds (i.e. fills) an entity property without tracking.
     *
     * @param string $name
     * @param mixed $value
     * @param boolean $strict
     * @throws InvalidArgumentException
     */
    public function add($name, $value, $strict = true)
    {
        $this->set($name, $value, $strict, false);
    }

    /**
     * Sets (i.e. updates) an entity property.
     *
     * @param string $name
     * @param mixed $value
     * @param boolean $strict
     * @param boolean $track
     * @throws InvalidArgumentException
     */
    public function set($name, $value, $strict = true, $track = true)
    {
        if (! (is_scalar($value) || is_null($value))) {
            throw new InvalidArgumentException( sprintf('%s property %s must be scalar', get_class($this), $name) );
        }

        if (property_exists($this, $name)) {
            if ($this->$name !== $value) {
                $this->$name = $value;

                if ($track && !in_array($name, $this->updatedProperties)) {
                    $this->updatedProperties[] = $name;
                }
            }
        } else if ($strict) {
            throw new InvalidArgumentException( sprintf('%s has no property %s', get_class($this), $name) );
        }
    }

    /**
     * Gets an entity property.
     *
     * @param string $name
     * @param mixed $default                The default value to return if property is empty or does not exist.
     *                                      If false, throws an exception in that case.
     * @param string $type                  Ensure value (if exist) is of this scalar or object type.
     * @return mixed
     * @throws RuntimeException
     */
    public function get($name, $default = null, $type = null)
    {
        if (property_exists($this, $name)) {
            $value = $this->$name;

            if ($value || is_numeric($value) || is_array($value)) {
                if ($type) {
                    if (! str_contains($type, '\\')) {
                        $assertion = 'is_' . $type;

                        if (! $assertion($value)) {
                            throw new RuntimeException( sprintf('%s is required to be of type %s', $name, $type) );
                        }
                    } else if (! ($value instanceof $type)) {
                        throw new RuntimeException( sprintf('%s is required to be of type %s', $name, $type) );
                    }
                }

                return $value;
            }
        }

        if ($default === false) {
            throw new RuntimeException( sprintf('%s has no property %s', get_class($this), $name) );
        }

        return $default;
    }

    /**
     * Gets an entity property or throws an exception if not found.
     *
     * @param string $name
     * @param string $type
     * @return mixed
     * @throws RuntimeException
     */
    public function need($name, $type = null)
    {
        return $this->get($name, false, $type);
    }

    /**
     * Adds (i.e. fills) an entity meta property without tracking.
     *
     * @param string $name
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function addMeta($name, $value)
    {
        $this->setMeta($name, $value, false);
    }

    /**
     * Sets (i.e. updates) an entity meta property.
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $track
     * @throws InvalidArgumentException
     */
    public function setMeta($key, $value, $track = true)
    {
        if (! (is_string($key) && strlen($key) > 1)) {
            throw new InvalidArgumentException('Meta key must be a string');
        }

        if (! (is_scalar($value) || is_null($value))) {
            throw new InvalidArgumentException('Meta value must be scalar');
        }

        if (strlen($value) == 0) {
            $value = null;
        }

        if (is_null($value)) {
            if (isset($this->meta[$key])) {
                unset($this->meta[$key]);

                if ($track && !in_array($key, $this->removedMetaProperties)) {
                    $this->removedMetaProperties[] = $key;
                }
            }
        } else {
            if (! isset($this->meta[$key]) || $this->meta[$key] !== $value) {
                if (isset($this->meta[$key]) && $track && !in_array($key, $this->updatedMetaProperties)) {
                    $this->updatedMetaProperties[] = $key;
                } else if (! isset($this->meta[$key]) && $track && !in_array($key, $this->insertedMetaProperties)) {
                    $this->insertedMetaProperties[] = $key;
                }

                $this->meta[$key] = $value;
            }
        }
    }

    /**
     * Gets an entity meta property.
     *
     * @param string $key
     * @param mixed $default                The default value to return if property is empty or does not exist.
     *                                      If false, throws an exception in that case.
     * @param string $type                  Ensure value (if exist) is of this scalar or object type.
     * @return mixed
     * @throws RuntimeException
     */
    public function getMeta($key, $default = null, $type = null)
    {
        if (isset($this->meta[$key])) {
            $value = $this->meta[$key];

            if ($value || is_numeric($value)) {
                if ($type) {
                    if (! str_contains($type, '\\')) {
                        $assertion = 'is_' . $type;

                        if (! $assertion($value)) {
                            throw new RuntimeException( sprintf('Meta property %s is required to be of type %s', $key, $type) );
                        }
                    } else if (! ($value instanceof $type)) {
                        throw new RuntimeException( sprintf('Meta property %s is required to be of type %s', $key, $type) );
                    }
                }

                return $value;
            }
        }

        if ($default === false) {
            throw new RuntimeException( sprintf('%s has no meta property %s', get_class($this), $key) );
        }

        return $default;
    }

    /**
     * Gets an entity meta property or throws an exception if not found.
     *
     * @param string $key
     * @param string $type
     * @return mixed
     * @throws RuntimeException
     */
    public function needMeta($key, $type = null)
    {
        return $this->getMeta($key, false, $type);
    }

    /**
     * Sets an extra item for this entity.
     *
     * @param string|int $key               Can be either a numeric or string index.
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function setExtra($key, $value)
    {
        if (! (is_numeric($key) || is_string($key))) {
            throw new InvalidArgumentException('Extra key must be numeric or string');
        }

        $this->extra[$key] = $value;
    }

    /**
     * Gets an extra item from this entity.
     *
     * @param string|int $key               Can be either a numeric or string index.
     * @param mixed $default                The default value to return if no extra item exist.
     *                                      If false, throws an exception in that case.
     * @param string $type                  Ensure value (if exist) is of this scalar or object type.
     * @return mixed
     * @throws RuntimeException
     */
    public function getExtra($key, $default = null, $type = null)
    {
        if (! (is_numeric($key) || is_string($key))) {
            throw new InvalidArgumentException('Extra key must be numeric or string');
        }

        if (isset($this->extra[$key])) {
            $value = $this->extra[$key];

            if ($value || is_numeric($value) || is_array($value)) {
                if ($type) {
                    if (! str_contains($type, '\\')) {
                        $assertion = 'is_' . $type;

                        if (! $assertion($value)) {
                            throw new RuntimeException( sprintf('Extra item %s is required to be of type %s', $key, $type) );
                        }
                    } else if (! ($value instanceof $type)) {
                        throw new RuntimeException( sprintf('Extra item %s is required to be of type %s', $key, $type) );
                    }
                }

                return $value;
            }
        }

        if ($default === false) {
            throw new RuntimeException( sprintf('%s has no extra item %s', get_class($this), $key) );
        }

        return $default;
    }

    /**
     * Gets an extra item from this entity or throws an exception if not found.
     *
     * @param string|int $key               Can be either a numeric or string index.
     * @param string $type
     * @return mixed
     * @throws RuntimeException
     */
    public function needExtra($key, $type = null)
    {
        return $this->getExtra($key, false, $type);
    }

    /**
     * Gets the entity's primary id name.
     *
     * @return string
     * @throws RuntimeException
     */
    public function getPrimary()
    {
        if (is_null($this->primary)) {
            throw new RuntimeException( sprintf('%s has no primary id specified', get_class($this)) );
        }

        return $this->primary;
    }

    /**
     * Gets the entity's primary id.
     *
     * @return int
     * @throws RuntimeException
     */
    public function getPrimaryID()
    {
        return $this->need($this->getPrimary());
    }

    /**
     * Resets the internal tracking arrays.
     */
    public function reset()
    {
        $this->updatedProperties = array();
        $this->updatedMetaProperties = array();
        $this->insertedMetaProperties = array();
        $this->removedMetaProperties = array();
    }

}
