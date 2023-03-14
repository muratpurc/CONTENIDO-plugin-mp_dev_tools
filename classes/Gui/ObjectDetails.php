<?php

/**
 * Gui ObjectDetails.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Gui;

use CONTENIDO\Plugin\MpDevTools\Reflection\ReflectionObject;

/**
 * Gui ObjectDetails class.
 */
class ObjectDetails extends AbstractBase
{

    /**
     * @var ReflectionObject
     */
    protected $reflectionObject;

    /**
     * @var \cHTMLDiv
     */
    protected $div;

    /**
     * @var object|null
     */
    protected $object;

    /**
     * Reflection filter.
     *
     * @var int|mixed
     */
    protected $filter;

    /**
     * Constructor.
     *
     * @param array $attr
     * @param int|mixed $filter Filter the results to include properties/methods with certain attributes, see
     *      {@link https://www.php.net/manual/en/class.reflectionproperty.php#reflectionproperty.constants.modifiers}.
     *      Default is {@see \ReflectionProperty::IS_PUBLIC}.
     */
    public function __construct(array $attr = [], $filter = null)
    {
        $this->div = new \cHTMLDiv();
        foreach ($attr as $key => $value) {
            $this->div->setAttribute($key, $value);
        }

        $this->filter = $filter ?: \ReflectionProperty::IS_PUBLIC;
    }

    /**
     * Sets the object to get the details for.
     *
     * @param $object
     * @return $this
     */
    public function setObject($object): ObjectDetails
    {
        $this->object = is_object($object) ? $object : null;
        return $this;
    }

    /**
     * Renders the object details.
     *
     * @return string
     */
    public function render(): string
    {
        // Call render function of super parent
        $mainOutput = $this->superRender();

        $this->div->setContent($this->renderObjectDetails());

        // Div wrapper
        $wrapper = new \cHTMLDiv('', 'mp_dev_tools_block mp_dev_tools_object_details');

        // Set fieldset table as content of the wrapper
        $wrapper->setContent($this->div);

        return $mainOutput . $wrapper->render();
    }

    protected function renderObjectDetails(): string
    {
        if (!is_object($this->object)) {
            return '';
        }

        // Get object details
        $reflection = new ReflectionObject($this->object);
        $name = $reflection->getName();
        $constants = $reflection->getConstants();
        $properties = $reflection->getProperties($this->filter);
        $magicProperties = $reflection->getMagicProperties();
        $methods = $reflection->getMethods($this->filter);

        // Class basics
        $result = $this->renderObjectDetailsBlock('Class name:', [$name]) . "\n";

        // Constants
        if (count($constants)) {
            $data = [];
            foreach ($constants as $constant => $value) {
                $data[] = "$constant = $value;";
            }
            $result .= $this->renderObjectDetailsBlock('Constants:', $data) . "\n";
        }

        // Properties
        if (count($properties)) {
            $data = [];
            foreach ($properties as $property) {
                $data[] = $property->getName();
            }
            $result .= $this->renderObjectDetailsBlock('Properties:', $data) . "\n";
        }

        // Magic properties
        if (count($magicProperties)) {
            $data = [];
            foreach ($magicProperties as $property) {
                $attribute = $property->getAttribute() ? ' (' . $property->getAttribute() . ')' : '';
                $data[] = "{$property->getName()}{$attribute}";
            }
            $result .= $this->renderObjectDetailsBlock('Magic properties (via _get() or _set()):', $data) . "\n";
        }

        // Methods
        if (count($methods)) {
            $data = [];
            foreach ($methods as $method) {
                $returnType = $method->getReturnType();
                // TODO Extract return type from doc comment of the function
                #$returnType = !empty($returnType) ? ': ' . $returnType : ': void';
                $returnType = !empty($returnType) ? ': ' . $returnType : '';
                $data[] = "{$method->getName()}(){$returnType}";
            }
            $result .= $this->renderObjectDetailsBlock('Methods:', $data) . "\n";
        }

        return '<pre>' . $result . '</pre>';
    }

    protected function renderObjectDetailsBlock(string $headline, array $contents): string
    {
        $entries = [];

        $entries[] = (new \cHTMLSpan($headline, 'mp_dev_tools_underline'))->render();
        foreach ($contents as $content) {
            $entries[] = (new \cHTMLSpan('- ' . $content))->render();
        }

        return implode("\n", $entries) . "\n";
    }

}