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

}