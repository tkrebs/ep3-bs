<?php

namespace Base\Manager;

use Base\Charon;
use Base\Table\OptionTable;
use InvalidArgumentException;
use RuntimeException;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Predicate\PredicateSet;

class OptionManager extends AbstractManager
{

    protected $optionTable;

    protected $locale;

    protected $options = array();

    /**
     * Creates a new option manager object.
     *
     * Preloads all available options from the database.
     *
     * @param OptionTable $optionTable
     * @param string $locale
     */
    public function __construct(OptionTable $optionTable, $locale)
    {
        $this->optionTable = $optionTable;

        $this->locale = $locale;

        /* Preload all available options that match the passed locale (or fall back to null ones). */

        $select = $optionTable->getSql()->select();

        $select->where(array(
            new IsNull('locale'),
            new Operator('locale', '=', $locale),
        ), PredicateSet::COMBINED_BY_OR);

        $select->order('locale DESC');

        try {
            $options = $this->optionTable->selectWith($select);
        } catch (RuntimeException $e) {
            include 'module/Base/Charon.php';

            Charon::carry('application', 'database', 1);
        }

        foreach ($options as $option) {
            if (! isset($this->options[$option->key])) {
                $this->options[$option->key] = $option->value;
            }
        }
    }

    /**
     * Sets an application option.
     *
     * If option key does not exist, it will be created.
     * If option value is null or empty, option will be deleted.
     *
     * @param string $key
     * @param mixed $value
     * @param string $locale
     * @throws InvalidArgumentException
     */
    public function set($key, $value, $locale = null)
    {
        if (! (is_string($key) && strlen($key) > 1)) {
            throw new InvalidArgumentException('Option key must be a string');
        }

        if (! (is_scalar($value) || is_null($value))) {
            throw new InvalidArgumentException('Option value must be scalar or null');
        }

        if (! (is_string($locale) || is_null($locale))) {
            throw new InvalidArgumentException('Locale value must be a string or null');
        }

        if (is_string($value) && strlen($value) == 0) {
            $value = null;
        }

        if (is_null($locale) || $locale == $this->locale) {
            $localTarget = true;
        } else {
            $localTarget = false;
        }

        $targetOptions = $this->optionTable->select(array('key' => $key, 'locale' => $locale))->toArray();

        if (count($targetOptions)) {
            $targetExists = true;
            $targetValue = $targetOptions[0]['value'];
        } else {
            $targetExists = false;
        }

        if ($targetExists) {
            if (is_null($value)) {
                $this->optionTable->delete(array('key' => $key, 'locale' => $locale));

                if ($localTarget) {
                    unset($this->options[$key]);
                }
            } else if ($targetValue !== $value) {
                $this->optionTable->update(array('value' => $value), array('key' => $key, 'locale' => $locale));

                if ($localTarget) {
                    $this->options[$key] = $value;
                }
            }
        } else {
            if (! is_null($value)) {
                $this->optionTable->insert(array('key' => $key, 'value' => $value, 'locale' => $locale));

                if ($localTarget) {
                    $this->options[$key] = $value;
                }
            }
        }

        $this->getEventManager()->trigger('set', $this, array('key' => $key, 'value' => $value, 'locale' => $locale));
    }

    /**
     * Gets an application option.
     *
     * @param string $key
     * @param mixed $default                The default value to return if option is empty or does not exist.
     *                                      If false, throws an exception in that case.
     * @param string $type                  Ensure value (if exist) is of this scalar or object type.
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function get($key, $default = null, $type = null)
    {
        if (! (is_string($key) && strlen($key) > 1)) {
            throw new InvalidArgumentException('Option key must be a string');
        }

        if (isset($this->options[$key])) {
            $value = $this->options[$key];

            if ($value || is_numeric($value)) {
                if ($type) {
                    if (! str_contains($type, '\\')) {
                        $assertion = 'is_' . $type;

                        if (! $assertion($value)) {
                            throw new RuntimeException( sprintf('Option %s is required to be of type %s', $key, $type) );
                        }
                    } else if (! ($value instanceof $type)) {
                        throw new RuntimeException( sprintf('Option %s is required to be of type %s', $key, $type) );
                    }
                }

                return $value;
            }
        }

        if ($default === false) {
            throw new RuntimeException( sprintf('Option %s does not exist', $key) );
        }

        return $default;
    }

    /**
     * Gets an application option or throws an exception if not found.
     *
     * @param string $key
     * @param string $type
     * @return mixed
     * @throws RuntimeException
     */
    public function need($key, $type = null)
    {
        return $this->get($key, false, $type);
    }

    /**
     * Loads an application option from the options table.
     *
     * @param string $key
     * @param string $locale
     * @return string
     */
    public function load($key, $locale = null)
    {
        $options = $this->optionTable->select(array('key' => $key, 'locale' => $locale));

        foreach ($options as $option) {
            return $option->value;
        }
    }

}
