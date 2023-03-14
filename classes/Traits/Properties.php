<?php

/**
 * Properties trait.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Traits;

/**
 * Properties trait.
 *
 * Provides functionality to manage generic properties.
 * Can be used everywhere you need generic properties.
 */
trait Properties
{

    /**
     * Common properties structure.
     *
     * NOTES:
     * - This has to be re-defined in the concrete class implementation.
     * - Only properties defined in `$properties` will be accepted within
     *   passed $properties to the constructor of the concrete class
     *   implementation.
     * - Only properties defined in `$properties` are accessible and can be
     *   managed via the getter/setter functions.
     * - Defined properties are public, they can be accessed from everywhere.
     *
     * @var  array
     */
    protected $properties = [];

    /**
     * Magic getter for properties.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->getProperty($name);
    }

    /**
     * Getter for property.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     * @return mixed|null
     */
    public function getProperty(string $name)
    {
        return $this->properties[$name] ?? null;
    }

    /**
     * Magic setter for properties.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $this->setProperty($name, $value);
    }

    /**
     * Setter for property.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setProperty(string $name, $value)
    {
        if (array_key_exists($name, $this->properties)) {
            $this->properties[$name] = $value;
        }
    }

    /**
     * Magic isset check for property.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->hasProperty($name);
    }

    /**
     * Checks if property exists.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    /**
     * Magic unset for an existing property.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     */
    public function __unset(string $name)
    {
        $this->unsetProperty($name);
    }

    /**
     * Unsets an existing property.
     * Note: See restrictions in {@see Properties::$properties}.
     *
     * @param string $name
     */
    public function unsetProperty(string $name)
    {
        if (isset($this->properties[$name])) {
            unset($this->properties[$name]);
        }
    }

}