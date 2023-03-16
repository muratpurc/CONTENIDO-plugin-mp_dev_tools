<?php

/**
 * Renders a category select.
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
 * Category select class.
 */
class CategorySelect extends AbstractBaseSelect
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
     * Renders a category select box, optional with articles.
     *
     * @param string $selCat Selected category, e.g. 'cat_<idcat>'.
     *      Single value or comma separated values.
     * @param int $level The category level to start from.
     * @param bool $withArticles Flag to render also articles.
     * @param string $selCatArt Selected category article, e.g. 'art_<idcatart>'.
     *      Single value or comma separated values.
     * @param bool $isCatDisabled Flag for disable category options.
     * @param string $optionLabel Label for the first option.
     * @return string
     * @throws \cDbException
     * @throws \cException
     * @throws \cInvalidArgumentException
     */
    public function render(
        string $selCat = '', int $level = 0, bool $withArticles = false,
        string $selCatArt = '', bool $isCatDisabled = false, string $optionLabel = ''
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

        $selCat = explode(',', $selCat);
        $selCatArt = explode(',', $selCatArt);

        // Additional database instance if category articles should be added too
        $db = $withArticles ? \cRegistry::getDb() : null;

        $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . '()';
        $sql = $comment . "
            SELECT
                a.idcat AS idcat,
                b.name AS name,
                b.visible AS visible,
                b.public AS public,
                c.level AS level
            FROM
                " . \cRegistry::getDbTableName('cat') . " AS a,
                " . \cRegistry::getDbTableName('cat_lang') . " AS b,
                " . \cRegistry::getDbTableName('cat_tree') . " AS c 
            WHERE
                a.idclient = " . $this->clientId . " AND
                b.idlang = " . $this->languageId . " AND
                b.idcat = a.idcat AND
                c.idcat = a.idcat";
        if ($level > 0) {
            $sql .= " AND c.level < " . $level;
        }
        $sql .= " ORDER BY c.idtree";

        $this->db->query($sql);

        if (!$this->db->numRows()) {
            $this->select->setDisabled(true);
        }

        while ($this->db->nextRecord()) {
            $idcat = \cSecurity::toInteger($this->db->f('idcat'));
            $spaces = str_repeat(self::LEVEL_SPACER, $this->db->f('level'));
            $identifier = 'cat_' . $idcat;

            $style = 'background-color: #efefef;';
            if ($this->db->f('visible') == 0 || $this->db->f('public') == 0) {
                $style .= 'color: #666;';
            }

            $title =  $spaces . '>' . urldecode($this->db->f('name'));
            $option = new \cHTMLOptionElement($title, $identifier);
            if (in_array($identifier, $selCat)) {
                $option->setSelected(true);
            }
            $option->setStyle($style);

            if ($isCatDisabled) {
                $option->setDisabled(true);
            }

            $this->select->appendOptionElement($option);

            if ($withArticles) {
                $this->addArticleOptions($idcat, $selCatArt, $spaces, $db);
            }
        }

        return $this->select->render();
    }

    /**
     * Selects the articles of the passed category and adds them to the select box.
     *
     * @param int $categoryId
     * @param array $selCatArt
     * @param string $spaces
     * @param \cDb $db
     * @return void
     * @throws \cDbException
     * @throws \cInvalidArgumentException
     */
    protected function addArticleOptions(int $categoryId, array $selCatArt, string $spaces, \cDb $db)
    {
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
                b.idcat = " . $categoryId . " AND
                a.idart = b.idart AND
                a.idlang = " . $this->languageId . "
            ORDER BY a.title
        ";

        $db->query($sql);

        while ($db->nextRecord()) {
            $searchCatArt = 'art_' . $db->f('idcatart');

            $title = $spaces . self::LEVEL_SPACER . substr(urldecode($db->f('title')), 0, 32);
            $option = new \cHTMLOptionElement($title, $searchCatArt);

            if (in_array($searchCatArt, $selCatArt)) {
                $option->setSelected(true);
            }

            $style = 'background-color:#fff;';
            if ($db->f('online') == 0) {
                $style .= 'color: #666;';
            }
            $option->setStyle($style);

            $this->select->appendOptionElement($option);
        }

    }

}