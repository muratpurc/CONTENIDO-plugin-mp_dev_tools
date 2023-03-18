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
     * @param string $selCatArt Selected category article, format is 'art_<idcatart>'.
     * @param string $selValue The selected value, format is '<idtype>:<typeid>'.
     * @param string $typeRange Single content type id (idtype) or comma separated list of ids, e.g. '1,2,3,4'
     * @param string $optionLabel Label for the first option.
     * @return string
     * @throws \cDbException
     * @throws \cException
     * @throws \cInvalidArgumentException
     */
    public function render(
        string $selCatArt, string $selValue, string $typeRange = '', string $optionLabel = ''
    ): string
    {
        $this->select = $this->createSelectInstance();

        if (empty($optionLabel)) {
            $optionLabel = i18n("Please choose");
        }

        $option = new \cHTMLOptionElement($optionLabel, '');
        $this->select->appendOptionElement($option);

        $selValue = explode(',', $selValue);

        $selCatArt = \cSecurity::toInteger(str_replace('art_', '', $selCatArt));

        if ($selCatArt > 0) {
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
            $sql .= "     c.idcatart = " . $selCatArt . "
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
                $identifier = $idtype . ':' . $typeid;
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

}