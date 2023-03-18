<?php

/**
 * Abstract base select.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Gui;

use CONTENIDO\Plugin\MpDevTools\MpDevTools;

/**
 * Abstract base select class.
 */
abstract class AbstractBaseSelect extends AbstractBase
{
    const LEVEL_SPACER = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    /**
     * @var \cHTMLSelectElement
     */
    protected $select;

    /**
     * @var \cDb
     */
    protected $db;

    /**
     * @var int
     */
    protected $clientId;

    /**
     * @var int
     */
    protected $languageId;

    /**
     * Select name.
     *
     * @var string
     */
    protected $name;

    /**
     * Select attributes.
     *
     * @var array
     */
    protected $attr;

    /**
     * Constructor.
     *
     * @param string $name Select box name
     * @param int $clientId Client id
     * @param int $languageId Language id
     * @param array $attr Attributes for the select box
     * @param \cDb|null $db Database instance
     */
    public function __construct(
        string $name, int $clientId, int $languageId, array $attr = [], \cDb $db = null
    )
    {
        $this->name = $name;
        $this->attr = $attr;
        $this->clientId = $clientId;
        $this->languageId = $languageId;
        $this->db = $db instanceof \cDb ? $db : \cRegistry::getDb();
    }

    /**
     * Creates the select element with the defined attributes.
     *
     * @return \cHTMLSelectElement
     */
    protected function createSelectInstance(): \cHTMLSelectElement
    {
        $select = new \cHTMLSelectElement($this->name);

        foreach ($this->attr as $key => $value) {
            if ($key === 'multiple') {
                $select->setMultiselect();
            } else {
                $select->setAttribute($key, $value);
            }
        }

        return $select;
    }

    /**
     * Updates the select element if it is of type multiple.
     * Renders a hidden field with the name of the select,
     * which will be filled with the selected values.
     *
     * @return string
     */
    protected function renderBase(): string
    {
        if ($this->select->getAttribute('multiple')) {
            $name = $this->select->getAttribute('name');
            $strLength = \cString::getStringLength($name);
            // Remove the array identifier added by the select
            if (\cString::getPartOfString($name, $strLength - 2, $strLength) === '[]') {
                $name = \cString::getPartOfString($name, 0, $strLength - 2);
            }

            $default = $this->select->getDefault();
            $hidden = new \cHTMLHiddenField($name, $default);

            /** @var MpDevTools $plugin */
            $plugin = \cRegistry::getAppVar('pluginMpDevTools');
            $id = uniqid($plugin::getFolderName() . '_field_');
            $hidden->setID($id);

            $name = uniqid($plugin::getFolderName() . '_select_');
            $this->select->setAttribute('name', $name)
                ->setAttribute('data-' . $plugin::getFolderName() . '-action-change', 'takeover_multiple_values')
                ->setAttribute('data-field-id', $id);

            return $hidden->render();
        }

        return '';
    }

}