<?php

/**
 * Backend helper trait.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Traits;

/**
 * Backend helper trait.
 *
 * Has some helper functions to use in backend context or to
 * check for several backend states.
 */
trait BackendHelper
{

    /**
     * Checks if the application is in the backend edit mode.
     *
     * @return bool
     */
    public function isBackendEditMode(): bool
    {
        return \cRegistry::isBackendEditMode();
    }

    /**
     * Checks if the application is in the backend visual edit mode.
     *
     * @return bool
     */
    public function isBackendVisualEditMode(): bool
    {
        // TODO Adapt to CONTENIDO > 4.10.1! {@see \cRegistry::isBackendVisualEditMode()}
        return \cRegistry::getBackendSessionId() && \cRegistry::getArea() === 'tpl_visual';
    }

    /**
     * Checks if the application is in the backend preview mode.
     *
     * @return bool
     */
    public function isBackendPreviewMode(): bool
    {
        if ($this->isBackendSession()) {
            /** @var \CONTENIDO\Plugin\MpDevTools\Http\Request $request */
            $request = \cRegistry::getAppVar('pluginMpDevToolsRequest');
            return $request->get('changeview') === 'prev';
        }

        return false;
    }

    /**
     * Checks if the application has a backend session.
     *
     * @return bool
     */
    public function isBackendSession(): bool
    {
        return !empty(\cRegistry::getBackendSessionId());
    }

    /**
     * Returns the backend area.
     * The return value will be an empty string if application has not a backend session.
     *
     * @return string
     */
    public function getBackendArea(): string
    {
        if ($this->isBackendSession()) {
            return \cSecurity::toString(\cRegistry::getArea() ?? '');
        };
        return '';
    }

    /**
     * Returns the backend action.
     * The return value will be an empty string if application has not a backend session.
     *
     * @return string
     */
    public function getBackendAction(): string
    {
        if ($this->isBackendSession()) {
            return \cSecurity::toString(\cRegistry::getAction() ?? '');
        };
        return '';
    }

}