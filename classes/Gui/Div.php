<?php

/**
 * Gui div.
 *
 * Provide features to generate div blocks in the CONTENIDO backends
 * template configuration area (aka `con_tplcfg`).
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
 * Gui div class.
 */
class Div extends AbstractBase
{

    /**
     * @var \cHTMLDiv
     */
    protected $div;

    /**
     * Constructor
     */
    public function __construct(array $attr = [])
    {
        $this->div = new \cHTMLDiv();
        foreach ($attr as $key => $value) {
            $this->div->setAttribute($key, $value);
        }
    }

    /**
     * Adds content to the div.
     *
     * @param string|object|array $content
     *         String with the content or a cHTML object to render
     *         or an array of strings / objects.
     * @return Div
     */
    public function addContent($content): Div
    {
        $this->div->appendContent($content);

        return $this;
    }

    /**
     * Renders the table.
     *
     * @return string
     */
    public function render(): string
    {
        // Call render function of super parent
        $mainOutput = $this->superRender();

        // Div wrapper
        $wrapper = new \cHTMLDiv('', 'mp_dev_tools_block mp_dev_tools_div');

        // Set fieldset table as content of the wrapper
        $wrapper->setContent($this->div);

        return $mainOutput . $wrapper->render();
    }

}