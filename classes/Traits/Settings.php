<?php

/**
 * Settings trait.
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
 * Settings trait.
 *
 * Provides functionality to manage effective settings.
 * Can be used everywhere you need effective settings.
 *
 * Namespaces settings type as follows, when properly initialized:
 * - In modules: module_<moduleName>
 * - In plugins: plugin_<pluginName>
 */
trait Settings
{

    /**
     * The settings namespace (prefix).
     * We need this to differentiate settings for modules or plugins.
     *
     * @var string
     */
    protected $settingsNamespace = '';

    /**
     * Returns the settings namespace.
     *
     * @return string
     */
    protected function getSettingsNameSpace()
    {
        return $this->settingsNamespace;
    }

    /**
     * Setter for settings namespace.
     *
     * @param string $namespace
     * @return void
     */
    protected function setSettingsNamespace(string $namespace)
    {
        $namespace = trim($namespace, '_');
        $this->settingsNamespace = $namespace;
    }

    /**
     * Settings getter.
     * Prefixes the $type with the defined namespace.
     *
     * @param string $name
     * @param mixed $default
     * @return bool|string|string[]
     * @throws \cDbException
     * @throws \cException
     */
    public function getEffectiveSetting(string $name, $default = '')
    {
        $type = $this->settingsNamespace;
        return \cEffectiveSetting::get($type, $name, $default);
    }

    /**
     * Settings setter.
     * Prefixes the $type with the defined namespace.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setEffectiveSetting(string $name, $value)
    {
        $type = $this->settingsNamespace;
        \cEffectiveSetting::set($type, $name, $value);
    }

    /**
     * Deletes setting.
     * Prefixes the $type with the defined namespace.
     *
     * @param string $name
     * @return void
     */
    public function deleteEffectiveSetting(string $name)
    {
        $type = $this->settingsNamespace;
        \cEffectiveSetting::delete($type, $name);
    }

}