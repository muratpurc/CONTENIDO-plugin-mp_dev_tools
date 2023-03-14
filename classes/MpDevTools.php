<?php

/**
 * MpDevTools plugin class.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools;

use CONTENIDO\Plugin\MpDevTools\Plugin\AbstractBase;

/**
 * MpDevTools plugin class.
 */
class MpDevTools extends AbstractBase
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Mp Dev Tools', 'mp_dev_tools');
    }

}