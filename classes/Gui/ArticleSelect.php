<?php

/**
 * Renders an article select.
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
 * Article select class.
 */
class ArticleSelect extends AbstractBaseSelect
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
     * Renders an article select box for a specific category.
     *
     * @param int $categoryId The id of the category to build the article select for.
     * @param string $selCatArt Selected category article, e.g. 'art_<idcatart>'.
     *      Single value or comma separated values.
     * @param string $optionLabel Label for the first option.
     * @return string
     * @throws \cDbException
     * @throws \cException
     * @throws \cInvalidArgumentException
     */
    public function render(
        int $categoryId, string $selCatArt, string $optionLabel = ''
    ): string
    {
        $this->select = new \cHTMLSelectElement($this->name);
        foreach ($this->attr as $key => $value) {
            $this->select->setAttribute($key, $value);
        }

        if (empty($optionLabel)) {
            $optionLabel = i18n("Please choose");
        }

        $option = new \cHTMLOptionElement($optionLabel, '');
        $this->select->appendOptionElement($option);

        $selCatArt = explode(',', $selCatArt);

        if ($categoryId > 0) {
            $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . '()';
            $sql = $comment . "
                SELECT
                    a.title AS title,
                    b.idcatart AS idcatart,
                    a.online AS online
                FROM
                    " . \cRegistry::getDbTableName('art_lang') . " AS a,
                    " . \cRegistry::getDbTableName('cat_art') . " AS b 
                WHERE
                    a.online = 1 AND
                    b.idcat = " . $categoryId . " AND
                    a.idart = b.idart AND
                    a.idlang = " . $this->languageId;

            $this->db->query($sql);

            if ($this->db->numRows() == 0) {
                $this->select->setDisabled(true);
            }

            while ($this->db->nextRecord()) {
                $idcatart = \cSecurity::toInteger($this->db->f('idcatart'));

                $title = substr(urldecode($this->db->f('title')), 0, 32);

                $option = new \cHTMLOptionElement($title, $idcatart);
                if (in_array($idcatart, $selCatArt)) {
                    $option->setSelected(true);
                }
                if ($this->db->f('online') == 0) {
                    $option->setStyle('color: #666;');
                }

                $this->select->appendOptionElement($option);
            }
        } else {
            $this->select->setDisabled(true);
        }

        return $this->select->render();
    }

}