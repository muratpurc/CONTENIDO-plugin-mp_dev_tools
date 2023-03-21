<?php

/**
 * Module token.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Module;

/**
 * Module token class.
 *
 * This class handles the access to the special tokens CMS_VAR and CMS_VALUE
 * used in module inputs/outputs.
 *
 * @property-read int index Token index
 * @property-read string var Token variable
 * @property-read mixed value Token value
 */
class CmsToken
{

    const TYPE_STRING = 'string';

    const TYPE_INT = 'int';

    const TYPE_BOOL = 'bool';

    /**
     * Container number.
     *
     * @var int
     */
    private $containerNumber;

    /**
     * Token index.
     *
     * @var int
     */
    private $index;

    /**
     * Token variable.
     *
     * @var string
     */
    private $var;

    /**
     * Token value.
     *
     * @var mixed|string
     */
    private $value;

    /**
     * Token variable type.
     *
     * @var string
     */
    private $type;

    /**
     * Class constructor.
     *
     * @param int $containerNumber Number of the container configured in the template,
     *      see also global variable $cCurrentContainer.
     * @param int $index Token index.
     * @param string $type Type 'string', 'int', or 'bool'. Converts the value
     *      to the defined type.
     */
    public function __construct(int $containerNumber, int $index, string $type = CmsToken::TYPE_STRING)
    {
        $this->containerNumber = $containerNumber;
        $this->index = $index;
        $this->var = $this->getTokenVar($index);

        if (in_array($this, [self::TYPE_STRING, self::TYPE_INT, self::TYPE_BOOL])) {
            $this->type = $type;
        } else {
            $this->type = self::TYPE_STRING;
        }

        $this->value = $this->getTokenValue($index);
    }

    /**
     * Returns the token value as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return \cSecurity::toString($this->getValue());
    }

    /**
     * Magic getter for properties index, var, and value.
     *
     * @param string $name
     * @return bool|int|string
     * @throws \cException
     */
    public function __get(string $name)
    {
        if ($name === 'index') {
            return $this->getIndex();
        } elseif ($name === 'var') {
            return $this->getVar();
        } elseif ($name === 'value') {
            return $this->getValue();
        } else {
            throw new \cException('Invalid property!');
        }
    }

    /**
     * Returns the token index.
     *
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Returns the full CMS_VAR token.
     *
     * @return string
     */
    public function getVar(): string
    {
        return $this->var;
    }

    /**
     * Returns the token value.
     *
     * @return string|int|bool
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the token value (CMS_VALUE) from the global variable.
     *
     * @param int $index
     * @return string
     */
    private function getTokenValue(int $index): string
    {
        $name = "C{$this->containerNumber}CMS_VALUE";

        if (isset($GLOBALS[$name]) && is_array($GLOBALS[$name]) && array_key_exists($index, $GLOBALS[$name])) {
            $value = $GLOBALS[$name][$index];
        } else {
            $value = '';
        }
        if ($this->type === self::TYPE_INT) {
            return \cSecurity::toInteger($value);
        } elseif ($this->type === self::TYPE_BOOL) {
            return \cSecurity::toBoolean($value);
        } else {
            return \cSecurity::toString($value);
        }
    }

    /**
     * Returns the token var (CMS_VAR).
     *
     * @param int $index
     * @return string
     */
    private function getTokenVar(int $index): string
    {
        return "C{$this->containerNumber}CMS_VAR[{$index}]";
    }

}