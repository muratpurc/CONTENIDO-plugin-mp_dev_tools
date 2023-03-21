<?php

/**
 * Renders a content type select.
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

use CONTENIDO\Plugin\MpDevTools\Module\CmsToken;

/**
 * Content type select class.
 */
class ContentTypeSelect extends AbstractBaseSelect
{

    /**
     * Constructor.
     *
     * @inheritdoc
     */
    public function __construct(
        string $name, int $clientId, int $languageId, array $attr = [], \cDb $db = null
    )
    {
        parent::__construct($name, $clientId, $languageId, $attr, $db);
    }

    /**
     * Renders a content-type select box.
     *
     * @param string $selCatArt Selected category article, format is 'idcatart:<idcatart>'.
     * @param string $selValue The selected value, format is '<idtype>:<typeid>'.
     * @param array $parameter Additional parameter as follows:
     *      [
     *          'optionLabel' => (string) Label for the first option.
     *          'noFirstOption' => (bool) Flag to not render the first option. Default `false`.
     *          'typeRange' => (string) Content type to render, single content type id (idtype)
     *              or comma separated list of ids, e.g. '1,2,3,4'.
     *      ]
     * @return string
     * @throws \cDbException
     * @throws \cException
     * @throws \cInvalidArgumentException
     */
    public function render(
        string $selCatArt, string $selValue, array $parameter = []
    ): string
    {
        $this->initializeSelect($parameter);

        $selValue = explode(self::VALUES_DELIMITER, $selValue);
        $selCatArt = ArticleSelect::getSelectedValues($selCatArt);
        $typeRange = $this->getParameter('typeRange', '');

        if (count($selCatArt) > 0) {
            $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . '()';
            $sql = $comment . "
                SELECT
                    a.typeid AS typeid,
                    a.value AS value,
                    a.idtype AS idtype,
                    d.type AS type,
                    d.description AS description
                FROM
                    " . \cRegistry::getDbTableName('content') . " AS a,
                    " . \cRegistry::getDbTableName('art_lang') . " AS b,
                    " . \cRegistry::getDbTableName('cat_art') . " AS c,
                    " . \cRegistry::getDbTableName('type') . " AS d
                WHERE
                    a.idtype    = d.idtype AND
                    a.idartlang = b.idartlang AND
                    b.idart     = c.idart AND
                    b.idlang    = " . $this->languageId . " AND ";

            if ($typeRange != '') {
                $sql .= "a.idtype IN (" . $typeRange . ") AND ";
            }
            $sql .= "     c.idcatart IN (" . implode(',', $selCatArt) . ")
              ORDER BY a.idtype, a.typeid";

            $this->db->query($sql);

            if ($this->db->numRows() == 0) {
                $this->select->setDisabled(true);
            }

            while ($this->db->nextRecord()) {
                $idtype = $this->db->f('idtype');
                $typeid = $this->db->f('typeid');
                $value = $this->db->f('value');

                // Identifier format: <idtype>:<typeid>
                $identifier = $idtype . self::ITEM_ID_VALUES_DELIMITER . $typeid;
                $content = !empty($value) ? substr(strip_tags(urldecode($value)), 0, 20) . '...' : '';
                $content = $this->db->f('type') . '[' . $typeid . ']: ' . $content;
                #$description = i18n($this->db->f("description"));

                $option = new \cHTMLOptionElement($content, $identifier);
                if (in_array($identifier, $selValue)) {
                    $option->setSelected(true);
                }
                $this->select->appendOptionElement($option);
            }
        } else {
            $this->select->setDisabled(true);
        }

        return parent::renderBase() . $this->select->render();
    }

    /**
     * Returns the selected values.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return array List of values where each item is
     *      `['idtype' => (int), 'typeid' => (int)]`.
     */
    public static function getSelectedValues($value): array
    {
        $rawValue = self::getSelectedRawValue($value);
        $return = [];

        // '<idtype>:<typeid>'
        $values = explode(self::VALUES_DELIMITER, $rawValue);
        foreach ($values as $item) {
            $itemIdValues = explode(self::ITEM_ID_VALUES_DELIMITER, $rawValue);
            if (count($itemIdValues) === 2) {
                $return[] = [
                    'idtype' => \cSecurity::toInteger($itemIdValues[0]),
                    'typeid' => \cSecurity::toInteger($itemIdValues[1]),
                ];
            }
        }

        return $return;
    }

}