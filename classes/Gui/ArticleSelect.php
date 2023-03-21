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

use CONTENIDO\Plugin\MpDevTools\Module\CmsToken;

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
     * @param int|string $categoryId The id of the category to build the article
     *       select for, or the string identifier, e.g. 'idcat:<idcat>'.
     * @param int|string $selCatArt Selected category-article id or the string
     *      identifier, e.g. 'idcatart:<idcatart>'.
     *      Single value or comma separated values.
     * @param array $parameter Additional parameter as follows:
     *      [
     *          'optionLabel' => (string) Label for the first option.
     *          'noFirstOption' => (bool) Flag to not render the first option. Default `false`.
     *      ]
     * @return string
     * @throws \cDbException
     * @throws \cInvalidArgumentException
     */
    public function render(
        $categoryId, string $selCatArt, array $parameter = []
    ): string
    {
        $this->initializeSelect($parameter);

        $selCatArt = explode(self::VALUES_DELIMITER, $selCatArt);
        array_map(function ($item) {
            return self::toCategoryArticleId($item);
        }, $selCatArt);

        $categoryId = CategorySelect::toCategoryId($categoryId);

        if ($categoryId > 0) {
            $this->addArticleOptions($categoryId, $selCatArt, 0, $this->db);
        } else {
            $this->select->setDisabled(true);
        }

        return parent::renderBase() . $this->select->render();
    }

    /**
     * Converts passed value to category-article id.
     *
     * @param int|string $value The category-article id or the string identifier, e.g. `idcatart:<idcatart>`.
     * @return int
     */
    public static function toCategoryArticleId($value): int
    {
        return CategorySelect::toCategoryArticleId($value);
    }

    /**
     * Returns the selected values.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return int[] List of selected article ids (idcatart)
     */
    public static function getSelectedValues($value): array
    {
        $rawValue = self::getSelectedRawValue($value);
        $return = [];

        // 'idcatart:<idcatart>'
        $values = explode(self::VALUES_DELIMITER, $rawValue);
        foreach ($values as $item) {
            if (is_numeric($item)) {
                $return[] = \cSecurity::toInteger($item);
            } else {
                $itemIdValues = explode(self::ITEM_ID_VALUES_DELIMITER, $item);
                if (count($itemIdValues) === 2 && $itemIdValues[0] === 'idcatart') {
                    $return[] = \cSecurity::toInteger($itemIdValues[1]);
                }
            }
        }

        return $return;
    }

    /**
     * Selects the articles of the passed category and adds them to the select box.
     *
     * @param int $categoryId
     * @param array $selCatArt
     * @param int $level
     * @param \cDb|null $db
     * @return void
     * @throws \cDbException
     * @throws \cInvalidArgumentException
     */
    protected function addArticleOptions(int $categoryId, array $selCatArt, int $level, \cDb $db = null)
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
                    a.online = 1 AND
                    b.idcat = " . $categoryId . " AND
                    a.idart = b.idart AND
                    a.idlang = " . $this->languageId . "
                ORDER BY a.title
            ";

        $db->query($sql);

        if ($db->numRows() == 0) {
            $this->select->setDisabled(true);
        }

        $indent = $this->getSpacer($level + 1);

        while ($db->nextRecord()) {
            $identifier = \cSecurity::toInteger($db->f('idcatart'));

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