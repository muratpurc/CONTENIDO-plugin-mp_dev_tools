<?php

/**
 * Gui trait.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Traits;

use CONTENIDO\Plugin\MpDevTools\Gui\ArrayDetails;
use CONTENIDO\Plugin\MpDevTools\Gui\ArticleSelect;
use CONTENIDO\Plugin\MpDevTools\Gui\CategorySelect;
use CONTENIDO\Plugin\MpDevTools\Gui\ContentTypeSelect;
use CONTENIDO\Plugin\MpDevTools\Gui\Div;
use CONTENIDO\Plugin\MpDevTools\Gui\FieldsetTable;
use CONTENIDO\Plugin\MpDevTools\Gui\ObjectDetails;
use CONTENIDO\Plugin\MpDevTools\Gui\Tabs;
use CONTENIDO\Plugin\MpDevTools\Gui\Table;
use CONTENIDO\Plugin\MpDevTools\Gui\UploadSelect;
use CONTENIDO\Plugin\MpDevTools\Module\CmsToken;

/**
 * Gui trait.
 *
 * Provides methods to retrieve gui class instances.
 */
trait Gui
{

    /**
     * Returns new Gui\Div instance.
     *
     * See {@see Div::__construct()}.
     *
     * @param array $attr
     * @return Div
     */
    public function getGuiDiv(array $attr = []): Div
    {
        return new Div($attr);
    }

    /**
     * Returns new Gui\Table instance.
     *
     * See {@see Table::__construct()}.
     *
     * @param array $attr
     * @return Table
     */
    public function getGuiTable(array $attr = []): Table
    {
        return new Table($attr);
    }

    /**
     * Returns new Gui\Table instance.
     *
     * See {@see FieldsetTable::__construct()}
     *
     * @param array $attr
     * @return FieldsetTable
     */
    public function getGuiFieldsetTable(array $attr = []): FieldsetTable
    {
        return new FieldsetTable($attr);
    }

    /**
     * Returns new Gui\ArticleSelect instance.
     *
     * See {@see ArticleSelect::__construct()}.
     *
     * @param string $name
     * @param int $clientId
     * @param int $languageId
     * @param array $attr
     * @param \cDb|null $db
     * @return ArticleSelect
     */
    public function getGuiArticleSelect(
        string $name, int $clientId, int $languageId, array $attr = [], \cDb $db = null
    ): ArticleSelect
    {
        return new ArticleSelect($name, $clientId, $languageId, $attr, $db);
    }

    /**
     * Returns selected values of a Gui\ArticleSelect.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return int[] List of selected article ids (idcatart)
     */
    public function getGuiArticleSelectValues(
        $value
    ): array
    {
        return ArticleSelect::getSelectedValues($value);
    }

    /**
     * Returns new Gui\CategorySelect instance.
     *
     * See {@see CategorySelect::__construct()}.
     *
     * @param string $name
     * @param int $clientId
     * @param int $languageId
     * @param array $attr
     * @param \cDb|null $db
     * @return CategorySelect
     */
    public function getGuiCategorySelect(
        string $name, int $clientId, int $languageId, array $attr = [], \cDb $db = null
    ): CategorySelect
    {
        return new CategorySelect($name, $clientId, $languageId, $attr, $db);
    }

    /**
     * Returns selected values of a Gui\CategorySelect.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return array List of values where each item can be
     *      `['idcat' => (int)]` or `['idcatart' => (int)]`.
     */
    public function getGuiCategorySelectValues(
        $value
    ): array
    {
        return CategorySelect::getSelectedValues($value);
    }

    /**
     * Returns new Gui\ContentTypeSelect instance.
     *
     * See {@see ContentTypeSelect::__construct()}.
     *
     * @param string $name
     * @param int $clientId
     * @param int $languageId
     * @param array $attr
     * @param \cDb|null $db
     * @return ContentTypeSelect
     */
    public function getGuiContentTypeSelect(
        string $name, int $clientId, int $languageId, array $attr = [], \cDb $db = null
    ): ContentTypeSelect
    {
        return new ContentTypeSelect($name, $clientId, $languageId, $attr, $db);
    }

    /**
     * Returns selected values of a Gui\ContentTypeSelect.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return array List of values where each item is
     *      `['idtype' => (int), 'typeid' => (int)]`.
     */
    public function getGuiContentTypeSelectValues(
        $value
    ): array
    {
        return ContentTypeSelect::getSelectedValues($value);
    }

    /**
     * Returns new Gui\UploadSelect instance.
     *
     * See {@see ContentTypeSelect::__construct()}.
     *
     * @param string $name
     * @param int $clientId
     * @param int $languageId
     * @param array $attr
     * @param \cDb|null $db
     * @return UploadSelect
     */
    public function getGuiUploadSelect(
        string $name, int $clientId, int $languageId, array $attr = [], \cDb $db = null
    ): UploadSelect
    {
        return new UploadSelect($name, $clientId, $languageId, $attr, $db);
    }

    /**
     * Returns selected values of a Gui\UploadSelect.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return array List of values where each item is
     *      `['idupl' => (int)] or ['iddbfs' => (int)]`.
     */
    public function getGuiUploadSelectValues(
        $value
    ): array
    {
        return UploadSelect::getSelectedValues($value);
    }

    /**
     * Returns new Gui\Tabs instance.
     *
     * See {@see Tabs::__construct()}.
     *
     * @param array $attr
     * @return Tabs
     */
    public function getGuiTabs(array $attr = []): Tabs
    {
        return new Tabs($attr);
    }

    /**
     * Returns new Gui\ArrayDetails instance.
     *
     * See {@see ObjectDetails::__construct()}.
     *
     * @param array $attr
     * @return ArrayDetails
     */
    public function getGuiArrayDetails(array $attr = []): ArrayDetails
    {
        return new ArrayDetails($attr);
    }

    /**
     * Returns new Gui\ObjectDetails instance.
     *
     * See {@see ObjectDetails::__construct()}.
     *
     * @param array $attr
     * @param $filter
     * @return ObjectDetails
     */
    public function getGuiObjectDetails(array $attr = [], $filter = null): ObjectDetails
    {
        return new ObjectDetails($attr, $filter);
    }

}