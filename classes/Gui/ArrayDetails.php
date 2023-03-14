<?php

/**
 * Gui ArrayDetails.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Gui;

/**
 * Gui ArrayDetails class.
 */
class ArrayDetails extends AbstractBase
{

    /**
     * @var \cHTMLDiv
     */
    protected $div;

    /**
     * @var array
     */
    protected $array;

    /**
     * Constructor.
     *
     * @param array $attr
     */
    public function __construct(array $attr = [])
    {
        $this->div = new \cHTMLDiv();
        foreach ($attr as $key => $value) {
            $this->div->setAttribute($key, $value);
        }
    }

    /**
     * Sets the array to get the details for.
     *
     * @param array $array
     * @return $this
     */
    public function setArray(array $array): ArrayDetails
    {
        $this->array = $array;
        return $this;
    }

    /**
     * Renders the array details.
     *
     * @return string
     */
    public function render(): string
    {
        // Call render function of super parent
        $mainOutput = $this->superRender();

        $this->div->setContent($this->renderArrayDetails());

        // Div wrapper
        $wrapper = new \cHTMLDiv('', 'mp_dev_tools_block mp_dev_tools_array_details');

        // Set fieldset table as content of the wrapper
        $wrapper->setContent($this->div);

        return $mainOutput . $wrapper->render();
    }

    protected function renderArrayDetails(): string
    {
        if (!is_array($this->array)) {
            return '';
        }

        if (empty($array)) {
            $result = '<pre>Array()</pre>';
        } else {
            $result = print_r($array, true);
            $textarea = new \cHTMLTextarea(uniqid($this->getModuleName() . '_'));
            $textarea->setClass('mp_dev_tools_code_area')
                ->setDisabled(true)
                ->setValue(print_r($result, true));
            $result = $textarea->render();
        }

        return $result;
    }

}