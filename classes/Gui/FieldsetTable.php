<?php

/**
 * Gui fieldset table.
 *
 * Provide features to generate collapsible module configuration fieldset tables
 * in the CONTENIDO backends template configuration area (aka `con_tplcfg`).
 *
 * Some functionalities are borrowed from the CONTENIDO modules
 * `Article List Reloaded` and `Terminliste v3`.
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
 * Gui fieldset table class.
 */
class FieldsetTable extends Table
{

    const LEGEND_TEMPLATE = '
<legend class="%s" data-mp_dev_tools-action="mp_dev_tools_toggle_fieldset_table">
    <h4><span class="mp_dev_tools_more_button %s">&raquo;</span>%s</h4>
</legend>
';

    /**
     * @var \cHTMLFieldset
     */
    private $fieldset;

    /**
     * @var string
     */
    private $legendCssClass;

    /**
     * @var string
     */
    private $legendTitle;

    /**
     * Constructor.
     *
     * @param array $attr Fieldset attributes.
     * @param array $tableAttr Fieldset table attributes.
     */
    public function __construct(array $attr = [], array $tableAttr = [])
    {
        parent::__construct($tableAttr);

        $this->fieldset = new \cHTMLFieldset();
        foreach ($attr as $key => $value) {
            $this->fieldset->setAttribute($key, $value);
        }

        $this->legendCssClass = '';
        $this->legendTitle = '';
    }

    /**
     * Set legend css class and title.
     *
     * @param string $legendCssClass
     * @param string $legendTitle
     * @return FieldsetTable
     */
    public function setLegend(string $legendCssClass, string $legendTitle): FieldsetTable
    {
        $this->legendCssClass = $legendCssClass;
        $this->legendTitle = $legendTitle;

        return $this;
    }

    /**
     * Renders the fieldset table, wraps it with an additional container,
     * having the css class name 'mp_dev_tools_fieldset_table'.
     *
     * @return string
     */
    public function render(): string
    {
        // Call render function of super parent
        $mainOutput = $this->superRender();

        // Fieldset table wrapper
        $wrapper = new \cHTMLDiv('', 'mp_dev_tools_block mp_dev_tools_fieldset_table');

        // Set fieldset table content
        $this->fieldset->setContent([
            sprintf(self::LEGEND_TEMPLATE, $this->legendCssClass, $this->legendCssClass, $this->legendTitle),
            new \cHTMLDiv($this->table , 'mp_dev_tools_content')
        ]);

        // Set fieldset table as content of the wrapper
        $wrapper->setContent($this->fieldset);

        // Render all
        return $mainOutput . $wrapper->render();
    }

}