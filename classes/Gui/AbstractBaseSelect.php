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

use CONTENIDO\Plugin\MpDevTools\Module\CmsToken;
use CONTENIDO\Plugin\MpDevTools\MpDevTools;

/**
 * Abstract base select class.
 */
abstract class AbstractBaseSelect extends AbstractBase
{

    /**
     * Spacer to represent the indentation for levels.
     */
    const LEVEL_SPACER = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    /**
     * Delimiter for multiple values.
     */
    const VALUES_DELIMITER = ',';

    /**
     * Delimiter for item ids.
     */
    const ITEM_ID_VALUES_DELIMITER = ':';

    /**
     * HTML entity for folder.
     */
    const FOLDER_SYMBOL = '&#128193;';

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
     * Additional parameter (options).
     *
     * @var array
     */
    protected $parameter;

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
        $this->parameter = [];
        $this->db = $db instanceof \cDb ? $db : \cRegistry::getDb();
    }

    protected function initializeSelect(array $parameter)
    {
        $this->parameter = $parameter;
        $this->select = $this->createSelect();
        $option = $this->createFirstOption();
        if ($option) {
            $this->select->appendOptionElement($option);
        }
    }

    /**
     * Creates the select element with the defined attributes.
     *
     * @return \cHTMLSelectElement
     */
    protected function createSelect(): \cHTMLSelectElement
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
     * Creates the first option element for the select box.
     *
     * @return \cHTMLOptionElement|null
     * @throws \cException
     */
    protected function createFirstOption()
    {
        if ($this->getParameter('noFirstOption', false)) {
            return null;
        }

        $optionLabel = $this->getParameter('optionLabel', i18n("Please choose"));
        return new \cHTMLOptionElement($optionLabel, '');
    }

    /**
     * Returns the list of selected values.
     *
     * @param CmsToken|string $value
     * @return array List of selected values. The list structure depends on the
     *     concrete select implementation.
     */
    abstract public static function getSelectedValues($value): array;

    /**
     * Returns the selected value in raw format.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return string The raw value.
     */
    protected static function getSelectedRawValue($value): string
    {
        return $value instanceof CmsToken ? $value->getValue() : $value;
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

    /**
     * Sets the value for a specific parameter by its key.
     *
     * @param string $key They key of parameter to set.
     * @param mixed $value The value to set.
     * @return void
     */
    protected function setParameter(string $key, $value)
    {
        $this->parameter[$key] = $value;
    }

    /**
     * Retrieves the parameter value by its key.
     *
     * @param string $key The parameter to get
     * @param mixed $default Default value to return if parameter is not set.
     * @return mixed|null
     */
    protected function getParameter(string $key, $default = null)
    {
        return $this->parameter[$key] ?? $default;
    }

    /**
     * Returns the spacer usable for rendered option elements.
     *
     * @param int $level
     * @return string
     */
    protected function getSpacer(int $level): string
    {
        return $level > 0 ? str_repeat(self::LEVEL_SPACER, $level) : '';
    }

    /**
     * Returns the folder symbol, either the configured one or the
     * defined one from the constant.
     *
     * @return bool|mixed|string|string[]
     * @throws \cDbException
     * @throws \cException
     */
    protected function getFolderSymbol()
    {
        static $folderSymbol;

        if (!isset($folderSymbol)) {
            /** @var MpDevTools $plugin */
            $plugin = \cRegistry::getAppVar('pluginMpDevTools');
            $folderSymbol = $plugin->getEffectiveSetting('select_option_folder_symbol', null);
            if (!is_string($folderSymbol)) {
                $folderSymbol = self::FOLDER_SYMBOL;
            }
        }

        return $folderSymbol;
    }

}