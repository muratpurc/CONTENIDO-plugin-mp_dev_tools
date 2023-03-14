<?php

/**
 * Gui table.
 *
 * Provide features to generate module configuration tables in the
 * CONTENIDO backends template configuration area (aka `con_tplcfg`).
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
 * Gui table class.
 */
class Table extends AbstractBase
{

    /**
     * @var \cHTMLTable
     */
    protected $table;

    /**
     * Constructor
     */
    public function __construct(array $attr = [])
    {
        $this->table = new \cHTMLTable();
        foreach ($attr as $key => $value) {
            $this->table->setAttribute($key, $value);
        }
    }

    /**
     * Adds a table row.
     *
     * @param array $contents
     * @param array $attr
     * @param array $contentAttr
     * @return Table
     */
    public function addRow(array $contents, array $attr = [], array $contentAttr = []): Table
    {
        $tr = new \cHTMLTableRow();
        foreach ($attr as $key => $value) {
            $tr->setAttribute($key, $value);
        }

        $trContent = [];
        foreach ($contents as $pos => $content) {
            $td = new \cHTMLTableData();
            $td->setContent($content);
            if (isset($contentAttr[$pos]) && is_array($contentAttr[$pos])) {
                foreach ($contentAttr[$pos] as $key => $value) {
                    $td->setAttribute($key, $value);
                }
            }
            $trContent[] = $td;
        }
        $tr->setContent($trContent);

        $this->table->appendContent($tr);

        return $this;
    }

    /**
     * Add full separator row.
     * All columns in the row will display a bottom separator line.
     *
     * @param array $contents
     * @param array $attr
     * @param array $contentAttr
     * @return Table
     */
    public function addFullSeparatorRow(array $contents, array $attr = [], array $contentAttr = []): Table
    {
        foreach ($contents as $pos => $content) {
            if (isset($contentAttr[$pos]['class'])) {
                $contentAttr[$pos]['class'] .= ' mp_dev_tools_separator';
            } else {
                $contentAttr[$pos]['class'] = 'mp_dev_tools_separator';
            }
        }

        $this->addRow($contents, $attr, $contentAttr);

        return $this;
    }

    /**
     * Add separator row.
     * All columns in the row will display a bottom separator line,
     * except the first one, which is considered as the label column.
     *
     * @param array $contents
     * @param array $attr
     * @param array $contentAttr
     * @return Table
     */
    public function addSeparatorRow(array $contents, array $attr = [], array $contentAttr = []): Table
    {
        foreach ($contents as $pos => $content) {
            if ($pos > 0) {
                if (isset($contentAttr[$pos]['class'])) {
                    $contentAttr[$pos]['class'] .= ' mp_dev_tools_separator';
                } else {
                    $contentAttr[$pos]['class'] = 'mp_dev_tools_separator';
                }
            }
        }

        $this->addRow($contents, $attr, $contentAttr);

        return $this;
    }

    /**
     * Add a contrast row.
     * All columns in the row will have a different background color.
     *
     * @param array $contents
     * @param array $attr
     * @param array $contentAttr
     * @return Table
     */
    public function addContrastRow(array $contents, array $attr = [], array $contentAttr = []): Table
    {
        if (isset($attr['class'])) {
            $attr['class'] .= ' mp_dev_tools_contrast';
        } else {
            $attr['class'] = 'mp_dev_tools_contrast';
        }

        $this->addRow($contents, $attr, $contentAttr);

        return $this;
    }

    /**
     * Add a row with submit link.
     * A click on the submit link will submit the template configuration form,
     * but with the form field 'send' set to '0'.
     * All columns in the row will have a different background color.
     *
     * @param array $contents
     * @param array $attr
     * @param array $contentAttr
     * @return Table
     */
    public function addSubmitRow(array $contents, array $attr = [], array $contentAttr = []): Table
    {
        foreach ($contents as $pos => $entry) {
            if (!empty($entry && is_string($entry))) {
                $contents[$pos] = $this->renderSubmitLink($entry);
            }
        }

        $this->addContrastRow($contents, $attr, $contentAttr);

        return $this;
    }

    /**
     * Renders a submit link.
     * A click on the submit link will submit the template configuration form,
     * but with the form field 'send' set to '0'.
     *
     * @param string $label
     * @return string
     */
    public function renderSubmitLink(string $label): string
    {
        $image = new \cHTMLImage('images/submit.gif');
        $image->setAlt($label);
        $link = new \cHTMLLink('javascript:void(ß)', $image, 'mp_dev_tools_submit_button');
        $link->disableAutomaticParameterAppend()
            ->setAttribute('data-mp_dev_tools-action', 'mp_dev_tools_submit');
        return $link->render();
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

        // Fieldset table wrapper
        $wrapper = new \cHTMLDiv('', 'mp_dev_tools_block mp_dev_tools_table');

        // Set fieldset table as content of the wrapper
        $wrapper->setContent($this->table);

        return $mainOutput . $wrapper->render();
    }

}