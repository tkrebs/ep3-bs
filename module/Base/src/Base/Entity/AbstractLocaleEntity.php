<?php

namespace Base\Entity;

use InvalidArgumentException;

abstract class AbstractLocaleEntity extends AbstractEntity
{

    protected $metaLocale = array();

    /**
     * Adds (i.e. fills) an entity meta property without tracking.
     *
     * @param string $name
     * @param mixed $value
     * @param string $locale
     * @throws InvalidArgumentException
     */
    public function addMeta($name, $value, $locale = null)
    {
        $this->setMeta($name, $value, $locale, false);
    }

    /**
     * Sets (i.e. updates) an entity meta property.
     *
     * @param string $key
     * @param mixed $value
     * @param string $locale
     * @param boolean $track
     * @throws InvalidArgumentException
     */
    public function setMeta($key, $value, $locale = null, $track = true)
    {
        parent::setMeta($key, $value, $track);

        if ($track) {
            if (! array_key_exists($key, $this->metaLocale)) {
                $this->metaLocale[$key] = $locale;
            } else {
                if (is_null($this->metaLocale[$key]) && ! is_null($locale)) {
                    $this->metaLocale[$key] = $locale;

                    $index = array_search($key, $this->updatedMetaProperties);
                    unset($this->updatedMetaProperties[$index]);
                    reset($this->updatedMetaProperties);

                    $this->insertedMetaProperties[] = $key;
                }
            }
        } else {
            $this->metaLocale[$key] = $locale;
        }
    }

    /**
     * Gets the locale for a given meta key.
     *
     * @param string $key
     * @return string|null
     */
    public function getMetaLocale($key)
    {
        $locale = $this->metaLocale[$key] ?? null;

        return $locale;
    }

}
