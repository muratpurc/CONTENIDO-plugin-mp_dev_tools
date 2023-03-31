<?php

/**
 * Array object.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools;

/**
 * Extended ArrayObject class.
 * Provides easy access to arrays.
 */
class ArrayObject extends \ArrayObject
{

    /**
     * Constructor.
     *
     * @param array $array Initial array data
     */
    public function __construct(array $array = [])
    {
        $this->setFlags(\ArrayObject::ARRAY_AS_PROPS);
        parent::__construct($array);
    }

    /**
     * Magic getter for array value.
     *
     * @param string|int|mixed $key Array index or key.
     * @return mixed|null
     */
    public function __get($key)
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : null;
    }

    /**
     * Getter for array value.
     *
     * @param string|int|mixed $key Array index or key.
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : null;
    }

    /**
     * Magic setter for array value.
     *
     * @param string|int|mixed $key Array index or key.
     * @param mixed $value
     * @return $this
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Setter for array value.
     *
     * @param string|int|mixed $key Array index or key.
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value): ArrayObject
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Magic isset check for array value.
     *
     * @param string|int|mixed $key Array index or key.
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Isset check for array value.
     *
     * @param string|int|mixed $key Array index or key.
     * @return bool
     */
    public function isset($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Magic unset for array index/key.
     *
     * @param string|int|mixed $key Array index or key.
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Unset for array index/key.
     *
     * @param string|int|mixed $key Array index or key.
     * @return $this
     */
    public function unset($key): ArrayObject
    {
        $this->offsetUnset($key);
        return $this;
    }

    /**
     * Returns value from a nested array structure using the "dot" notation.
     *
     * @param string $key The array index/key or by "dot" concatenated indexes/keys
     *      to retrieve data from a nested array structure.
     * @param mixed $default
     * @return mixed|null
     */
    public function fetch(string $key, $default = null)
    {
        if ($key === '') {
            return $default;
        }

        $keys = explode('.', $key);
        $data = $default;

        foreach ($keys as $_pos => $_key) {
            if ($_pos === 0) {
                $data = $this->get($_key);
            } elseif (is_array($data)) {
                $data = $data[$_key] ?? null;
            } else {
                break;
            }
        }

        return $data;
    }

    /**
     * Returns the value at the specified index/key.
     *
     * @param string|int|mixed $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return \property_exists($this, (string)$key) ? parent::offsetGet($key) : null;
    }

}
