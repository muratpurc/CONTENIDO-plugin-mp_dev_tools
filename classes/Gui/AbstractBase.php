<?php

/**
 * Abstract base for all Gui classes.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Gui;

use CONTENIDO\Plugin\MpDevTools\MpDevTools;

/**
 * Abstract base class for all Gui classes.
 */
abstract class AbstractBase
{

    /**
     * Renders the CSS and the JS required for the "Mp Dev Tools" plugin.
     *
     * @return string
     */
    public function superRender(): string
    {
        return $this->renderPluginCssJs();
    }

    /**
     * Render the CSS and the JS only once.
     *
     * @return string
     */
    protected function renderPluginCssJs(): string
    {
        /** @var MpDevTools $plugin */
        $plugin = \cRegistry::getAppVar('pluginMpDevTools');
        return $plugin->renderMainPluginStyleAndScript();
    }

}