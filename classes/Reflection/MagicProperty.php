<?php

/**
 * MagicProperty.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Reflection;

/**
 * MagicProperty class.
 *
 * Represents property annotations (magic property) details extracted from
 * a doc comment block of a class.
 */
class MagicProperty
{

    const READ = 'read';

    const WRITE = 'write';

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * Constructor.
     *
     * @param string $attribute
     * @param string $type
     * @param string $name
     * @param string $description
     */
    public function __construct(string $attribute, string $type, string $name, string $description = '')
    {
        $this->attribute = in_array($attribute, ['read', 'write', '']) ? $attribute : '';
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

}