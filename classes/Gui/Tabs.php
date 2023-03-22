<?php

/**
 * Gui tabs.
 *
 * Provide features to generate tabs blocks in the CONTENIDO backends
 * template configuration area (aka `con_tplcfg`).
 * Works with jQuery UI Tabs, see also mp_dev_tools.js.
 *
 * Usage:
 * ------
 * <pre>
 * $module = new MyModule();
 * $tabs = $module->getGuiTabs();
 * $tabs->setHeader('key_1', 'Tab header');
 * $tabs->addContent('key_1', 'Tab content');
 * $tabs->setHeader('key_2', 'Tab header 2');
 * $tabs->addContent('key_2', 'Tab content 2');
 * echo $tabs->render();
 * </pre>
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
 * Gui tabs class.
 */
class Tabs extends AbstractBase
{

    /**
     * @var \cHTMLDiv
     */
    protected $div;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var \cHTMLDiv[]
     */
    protected $contents = [];

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
     * Sets the tab header.
     *
     * @param string $key
     * @param string $title
     *
     * @return $this
     */
    public function setHeader(string $key, string $title): Tabs
    {
        $this->headers[$key] = $title;

        return $this;
    }

    /**
     * Sets the tab attributes.
     *
     * @param string $key
     * @param array  $attr
     *
     * @return $this
     */
    public function setAttributes(string $key, array $attr): Tabs
    {
        $this->initializeContent($key);
        foreach ($attr as $key => $value) {
            $this->contents[$key]->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Adds content to the tab.
     *
     * @param string $key
     * @param string|object|array $content
     *         String with the content or a cHTML object to render
     *         or an array of strings / objects.
     * @return $this
     */
    public function addContent(string $key, $content): Tabs
    {
        $this->initializeContent($key);
        $this->contents[$key]->appendContent($content);

        return $this;
    }

    /**
     * Renders the tabs.
     *
     * @return string
     */
    public function render(): string
    {
        // Call render function of super parent
        $mainOutput = $this->superRender();

        // Div wrapper
        $wrapper = new \cHTMLDiv('', 'mp_dev_tools_block mp_dev_tools_tabs');

        // Set header and contents as content of the wrapper
        $header = $this->buildHeader();
        $wrapper->appendContent($header);
        $wrapper->appendContent(array_values($this->contents));

        return $mainOutput . $wrapper->render();
    }

    protected function initializeContent(string $key)
    {
        if (!isset($this->contents[$key])) {
            $id = 'mp_dev_tools_tabs_' . md5($key);
            $this->contents[$key] = new \cHTMLDiv();
            $this->contents[$key]->setID($id);
        }
    }

    protected function buildHeader(): \cHTMLList
    {
        $list = new \cHTMLList();
        foreach ($this->headers as $key => $header) {
            $id = 'mp_dev_tools_tabs_' . md5($key);
            $listItem = new \cHTMLListItem();
            $link = new \cHTMLLink('#' . $id, $header);
            $link->disableAutomaticParameterAppend();
            $listItem->setContent($link);
            $list->appendContent($listItem);
        }

        return $list;
    }

}