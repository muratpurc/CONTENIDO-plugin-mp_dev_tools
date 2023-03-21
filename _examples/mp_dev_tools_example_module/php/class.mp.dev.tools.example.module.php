<?php

/**
 * MpDevToolsExampleModule class file.
 *
 * @package     Module
 * @subpackage  MpDevToolsExampleModule
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

use CONTENIDO\Plugin\MpDevTools\Gui\FieldsetTable;
use CONTENIDO\Plugin\MpDevTools\Gui\Table;
use CONTENIDO\Plugin\MpDevTools\Module\AbstractBase;

/**
 * Mp Dev Tools example module class.
 *
 * @property cDb     db                Database instance
 * @property array   i18n              Translation array
 * @property string  myCustomProperty  Custom property
 */
class MpDevToolsExampleModule extends AbstractBase
{

    /**
     * Module properties structure.
     *
     * See {@see AbstractBase::$baseProperties} for base properties. Only
     * properties being defined here and in the base class ($baseProperties)
     * will be taken over to the $properties structure.
     *
     * @inheritdoc
     */
    protected $properties = [
        'db' => null,
        'i18n' => [],
        'myCustomProperty' => '',
    ];

    /**
     * Constructor, sets some properties.
     *
     * @param array $properties Properties array {@see MpDevToolsExampleModule::$properties}
     * @throws cException
     */
    public function __construct(array $properties)
    {
        parent::__construct('mp_dev_tools_example_module', $properties);
    }

    /**
     * Returns list of all idtypes from type table.
     *
     * @return array
     * @throws cDbException
     * @throws cInvalidArgumentException
     */
    public function getContentTypeIds(): array
    {
        $idtypes = [];

        $sql = "SELECT `idtype` FROM `%s` ORDER BY `idtype`";
        $this->db->query($sql, cRegistry::getDbTableName('type'));
        while ($this->db->nextRecord()) {
            $idtypes[] = cSecurity::toInteger($this->db->f('idtype'));
        }

        return $idtypes;
    }

    /**
     * @param FieldsetTable|Table $table
     * @param string $codeLabel
     * @param $result
     * @return $this
     */
    public function addCodeRow($table, string $codeLabel, $result): MpDevToolsExampleModule
    {
        $codeRow = '<pre><strong>' . $codeLabel . '</strong></pre>';

        $resultLabel = $this->i18n['LBL_RESULT'];

        if (is_object($result)) {
            $objectDetails = $this->getGuiObjectDetails();
            $result = $objectDetails->setObject($result)->render();
        } elseif (is_array($result)) {
            $arrayDetails = $this->getGuiArrayDetails();
            $result = $arrayDetails->setArray($result)->render();
        } elseif (is_bool($result)) {
            $result = $result === true ? 'true' : 'false';
            $result = '<pre>' . $result . '</pre>';
        } else {
            $result = '<pre>' . $result . '</pre>';
        }

        $table->addRow(
            [$codeRow], [], [['colspan' => '2']]
        );

        $table->addFullSeparatorRow(
            [$resultLabel, $result], [], [[], ['style' => 'width:100%;']]
        );

        return $this;
    }

    /**
     * Some styles for this module.
     *
     * @return string
     */
    public function renderModuleInputStyles(): string
    {
        static $stylesRendered;

        if (isset($stylesRendered)) {
            return '';
        }
        $stylesRendered = true;

        return '
<style>
    .mp_dev_tools_fieldset_table .mp_dev_tools_content pre {
        margin: 0;
        padding: 0;
    }
    .mp_dev_tools_fieldset_table .mp_dev_tools_underline {
        text-decoration: underline;
    }
</style>
        ';
    }

}