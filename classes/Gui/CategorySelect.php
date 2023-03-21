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

use CONTENIDO\Plugin\MpDevTools\Module\CmsToken;

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
     * @param string $selCat Selected category, e.g. 'idcat:<idcat>'.
     *      Single value or comma separated values.
     * @param string $selCatArt Selected category article, e.g. 'idcatart:<idcatart>'.
     *      Single value or comma separated values.
     * @param array $parameter Additional parameter as follows:
     *      [
     *          'optionLabel' => (string) Label for the first option.
     *          'noFirstOption' => (bool) Flag to not render the first option. Default `false`.
     *          'startLevel' => (int) The category level to start from. Default `0`.
     *          'withArticles' => (bool) Flag to render also articles. Default `false`.
     *          'disableCategories' => (bool) Flag for disable category options. Default `false`.
     *      ]
     * @return string
     * @throws \cDbException
     * @throws \cException
     * @throws \cInvalidArgumentException
     */
    public function render(
        string $selCat = '', string $selCatArt = '', array $parameter = []
    ): string
    {
        $this->initializeSelect($parameter);

        $selCat = explode(self::VALUES_DELIMITER, $selCat);
        $selCatArt = explode(self::VALUES_DELIMITER, $selCatArt);
        $startLevel = $this->getParameter('startLevel', 0);
        $withArticles = $this->getParameter('withArticles', false);

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
        if ($startLevel > 0) {
            $sql .= " AND c.level < " . $startLevel;
        }
        $sql .= " ORDER BY c.idtree";

        $this->db->query($sql);

        if (!$this->db->numRows()) {
            $this->select->setDisabled(true);
        }

        $folderSymbol = $this->getFolderSymbol();
        $disableCategories = $this->getParameter('disableCategories', false);

        while ($this->db->nextRecord()) {
            $idcat = \cSecurity::toInteger($this->db->f('idcat'));
            $identifier = 'idcat' . self::ITEM_ID_VALUES_DELIMITER . $idcat;
            $level = \cSecurity::toInteger($this->db->f('level'));
            $indent = $this->getSpacer($level);

            $cssClasses = ['mp_dev_tools_option_group'];
            if ($this->db->f('visible') == 0 || $this->db->f('public') == 0) {
                $cssClasses[] = 'mp_dev_tools_option_limited';
            }

            $title =  $indent . $folderSymbol . ' ' . urldecode($this->db->f('name'));
            $option = new \cHTMLOptionElement($title, $identifier);
            if (in_array($identifier, $selCat)) {
                $option->setSelected(true);
            }

            if (!empty($cssClasses)) {
                $option->setClass(implode(' ', $cssClasses));
            }
            if ($disableCategories) {
                $option->setDisabled(true);
            }

            $this->select->appendOptionElement($option);

            if ($withArticles) {
                $this->addArticleOptions($idcat, $selCatArt, $level, $db);
            }
        }

        return parent::renderBase() . $this->select->render();
    }

    /**
     * Returns the selected values.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return array List of values where each item can be
     *      `['idcat' => (int)]` or `['idcatart' => (int)]`.
     */
    public static function getSelectedValues($value): array
    {
        $rawValue = self::getSelectedRawValue($value);
        $return = [];

        // 'idcat:<idcat>' or 'idcatart:<idcatart>'
        $values = explode(self::VALUES_DELIMITER, $rawValue);
        foreach ($values as $item) {
            $itemIdValues = explode(self::ITEM_ID_VALUES_DELIMITER, $item);
            if (count($itemIdValues) === 2 && in_array($itemIdValues[0], ['idcat', 'idcatart'])) {
                if ($itemIdValues[0] === 'idcatart') {
                    $return[] = ['idcatart' => \cSecurity::toInteger($itemIdValues[1])];
                } elseif ($itemIdValues[0] === 'idcat') {
                    $return[] = ['idcat' => \cSecurity::toInteger($itemIdValues[1])];
                }
            }
        }

        return $return;
    }

    /**
     * Converts passed value to category id.
     *
     * @param int|string $value The category id or the string identifier, e.g. `idcat:<idcat>`.
     * @return int
     */
    public static function toCategoryId($value): int
    {
        if (is_numeric($value)) {
            return \cSecurity::toInteger($value);
        } else {
            $ids = CategorySelect::getSelectedValues($value);
            if (count($ids) === 1 && isset($ids[0]['idcat'])) {
                return $ids[0]['idcat'];
            }
        }

        return 0;
    }

    /**
     * Converts passed value to category-article id.
     *
     * @param int|string $value The category-article id or the string identifier, e.g. `idcatart:<idcatart>`.
     * @return int
     */
    public static function toCategoryArticleId($value): int
    {
        if (is_numeric($value)) {
            return \cSecurity::toInteger($value);
        } else {
            $ids = CategorySelect::getSelectedValues($value);
            if (count($ids) === 1 && isset($ids[0]['idcatart'])) {
                return $ids[0]['idcatart'];
            }
        }

        return 0;
    }

    /**
     * Selects the articles of the passed category and adds them to the select box.
     *
     * @param int $categoryId
     * @param array $selCatArt
     * @param int $level
     * @param \cDb $db
     * @return void
     * @throws \cDbException
     * @throws \cInvalidArgumentException
     */
    protected function addArticleOptions(int $categoryId, array $selCatArt, int $level, \cDb $db)
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

        $indent = $this->getSpacer($level + 1);

        while ($db->nextRecord()) {
            $identifier = 'idcatart' . self::ITEM_ID_VALUES_DELIMITER . $db->f('idcatart');

            $cssClasses = [];
            if ($db->f('online') == 0) {
                $cssClasses[] = 'mp_dev_tools_option_limited';
            }

            $title = $indent . substr(urldecode($db->f('title')), 0, 32);
            $option = new \cHTMLOptionElement($title, $identifier);

            if (in_array($identifier, $selCatArt)) {
                $option->setSelected(true);
            }

            if (!empty($cssClasses)) {
                $option->setClass(implode(' ', $cssClasses));
            }

            $this->select->appendOptionElement($option);
        }

    }

}