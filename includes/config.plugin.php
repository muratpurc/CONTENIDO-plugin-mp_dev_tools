<?php

/**
 * Plugin "Mp Dev Tools" initialization file.
 *
 * This file will be included by CONTENIDO plugin loader routine, and the content
 * of this file ensures that the plugin will be initialized correctly.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

global $cfg;

// Plugin folder name
$pluginName = basename(dirname(__DIR__, 1));

// Register plugin in plugins configuration
$cfg['plugins'][$pluginName] = cRegistry::getBackendPath() . $cfg['path']['plugins'] . "$pluginName/";

// Relative contenido path from project root
$contenidoPath = str_replace($cfg['path']['frontend'] . '/', '', cRegistry::getBackendPath());

// Path to plugin classes
$pluginClassesPath = $contenidoPath . $cfg['path']['plugins'] . "$pluginName/classes";

cAutoload::addClassmapConfig([
    'CONTENIDO\\Plugin\\MpDevTools\\MpDevTools' => $pluginClassesPath . '/MpDevTools.php',
    'CONTENIDO\\Plugin\\MpDevTools\\ContentExtractor\\HtmlImage' => $pluginClassesPath . '/ContentExtractor/HtmlImage.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Client\\Info' => $pluginClassesPath . '/Client/Info.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\AbstractBase' => $pluginClassesPath . '/Gui/AbstractBase.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\AbstractBaseSelect' => $pluginClassesPath . '/Gui/AbstractBaseSelect.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\ArrayDetails' => $pluginClassesPath . '/Gui/ArrayDetails.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\ArticleSelect' => $pluginClassesPath . '/Gui/ArticleSelect.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\CategorySelect' => $pluginClassesPath . '/Gui/CategorySelect.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\ContentTypeSelect' => $pluginClassesPath . '/Gui/ContentTypeSelect.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\FieldsetTable' => $pluginClassesPath . '/Gui/FieldsetTable.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\ObjectDetails' => $pluginClassesPath . '/Gui/ObjectDetails.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Gui\\Table' => $pluginClassesPath . '/Gui/Table.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Http\\Request' => $pluginClassesPath . '/Http/Request.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Module\\CmsToken' => $pluginClassesPath . '/Module/CmsToken.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Module\\AbstractBase' => $pluginClassesPath . '/Module/AbstractBase.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Reflection\\MagicProperty' => $pluginClassesPath . '/Reflection/MagicProperty.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Reflection\\ReflectionObject' => $pluginClassesPath . '/Reflection/ReflectionObject.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Plugin\\AbstractBase' => $pluginClassesPath . '/Plugin/AbstractBase.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Traits\\BackendHelper' => $pluginClassesPath . '/Traits/BackendHelper.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Traits\\ClientInfo' => $pluginClassesPath . '/Traits/ClientInfo.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Traits\\Gui' => $pluginClassesPath . '/Traits/Gui.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Traits\\Properties' => $pluginClassesPath . '/Traits/Properties.php',
    'CONTENIDO\\Plugin\\MpDevTools\\Traits\\Settings' => $pluginClassesPath . '/Traits/Settings.php',
]);

// Register request instance as an application variable
cRegistry::setAppVar('pluginMpDevToolsRequest', new \CONTENIDO\Plugin\MpDevTools\Http\Request());

// Register plugin instance as an application variable
cRegistry::setAppVar('pluginMpDevTools', new \CONTENIDO\Plugin\MpDevTools\MpDevTools());

unset($pluginName, $contenidoPath, $pluginClassesPath);
