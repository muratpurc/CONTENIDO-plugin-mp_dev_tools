<?php

/**
 * Base plugin class.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Plugin;

use CONTENIDO\Plugin\MpDevTools\Http\Request;
use CONTENIDO\Plugin\MpDevTools\Traits\BackendHelper;
use CONTENIDO\Plugin\MpDevTools\Traits\ClientInfo;
use CONTENIDO\Plugin\MpDevTools\Traits\Gui;
use CONTENIDO\Plugin\MpDevTools\Traits\Properties;
use CONTENIDO\Plugin\MpDevTools\Traits\Settings;

/**
 * Base plugin class.
 *
 * Provides common functionality which can be derived and used
 * by other plugin classes.
 *
 * @property int clientId  Client id
 * @property int languageId Language id
 * @property array cfg Global configuration array
 * @property array cfgClient Clients configuration array
 */
#[AllowDynamicProperties]
abstract class AbstractBase
{

    use BackendHelper;
    use ClientInfo;
    use Gui;
    use Properties;
    use Settings;

    /**
     * Base plugin properties structure.
     *
     * @var  array
     */
    private $baseProperties = [
        'clientId' => 0,
        'languageId' => 0,
        'cfg' => [],
        'cfgClient' => [],
    ];

    /**
     * @inheritdoc
     * @var array|mixed
     */
    protected $properties = [];

    /**
     * Plugin name.
     *
     * @var string
     */
    private static $name;

    /**
     * Plugin folder name.
     *
     * @var string
     */
    private static $folderName;

    /**
     * Plugin information instance, contains the corresponding
     * record from the plugins table.
     *
     * @var \stdClass
     */
    protected $pluginInfo;

    /**
     * HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Constructor.
     *
     * @param string $name Plugin name.
     * @param string $folderName Plugin folder name.
     * @param array $properties Associative properties array
     *      {@see AbstractBase::$properties} or properties.
     * @throws \cException
     */
    public function __construct(string $name, string $folderName, array $properties = [])
    {
        if (!isset(self::$name)) {
            self::$name = $name;
            self::$folderName = $folderName;
        }

        $this->initializeProperties($properties);

        if (empty(self::$name) || empty(self::$folderName)) {
            throw new \cException('Plugin name and/or plugin folder name is empty!');
        }

        $this->setSettingsNamespace('plugin_' . self::$folderName);

        $this->request = \cRegistry::getAppVar('pluginMpDevToolsRequest');
    }

    /**
     * Returns the HTTP request instance.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns plugin name.
     *
     * @return string
     */
    public static function getName(): string
    {
        return self::$name;
    }

    /**
     * Returns plugin folder name.
     *
     * @return string
     */
    public static function getFolderName(): string
    {
        return self::$folderName;
    }

    /**
     * Returns path to the plugin folder.
     *
     * @return string
     */
    public static function getPath(): string
    {
        $cfg = \cRegistry::getConfig();

        $path = \cRegistry::getBackendPath() . $cfg['path']['plugins'];
        $path .= self::$folderName . '/';

        return $path;
    }

    /**
     * Returns URL to the plugin folder.
     *
     * @return string
     */
    public static function getUrl(): string
    {
        $cfg = \cRegistry::getConfig();

        $path = \cRegistry::getBackendUrl() . $cfg['path']['plugins'];
        $path .= self::$folderName . '/';

        return $path;
    }

    /**
     * Returns plugin translation.
     *
     * @param string $key
     * @return string
     */
    public static function i18n(string $key): string
    {
        try {
            return i18n($key, self::$name);
        } catch (\cException $e) {
            return 'Plugin translation not found: `' . $key . '`';
        }
    }

    /**
     * Returns plugin path.
     *
     * @return string
     */
    public function getPluginPath(): string
    {
        return self::getPath();
    }

    /**
     * Returns the images path of the plugin.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     */
    public function getImagePath(string $file = ''): string
    {
        return self::getPath() . 'images/' . $file;
    }

    /**
     * Returns the images URL of the plugin.
     * If a file is set, it will return the URL to the file.
     *
     * @param string $file
     * @return string
     */
    public function getImageUrl(string $file = ''): string
    {
        return self::getUrl() . 'images/' . $file;
    }

    /**
     * Returns the includes path of the plugin.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     */
    public function getIncludePath(string $file = ''): string
    {
        return self::getPath() . 'includes/' . $file;
    }

    /**
     * Returns the styles (css) path of the plugin.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     */
    public function getStylePath(string $file = ''): string
    {
        return self::getPath() . 'styles/' . $file;
    }

    /**
     * Returns the styles URL of the plugin.
     * If a file is set, it will return the URL to the file.
     *
     * @param string $file
     * @return string
     */
    public function getStyleUrl(string $file = ''): string
    {
        return self::getUrl() . 'styles/' . $file;
    }

    /**
     * Returns the scripts (JavaScript) path of the plugin.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     */
    public function getScriptPath(string $file = ''): string
    {
        return self::getPath() . 'scripts/' . $file;
    }

    /**
     * Returns the scripts URL of the plugin.
     * If a file is set, it will return the URL to the file.
     *
     * @param string $file
     * @return string
     */
    public function getScriptUrl(string $file = ''): string
    {
        return self::getUrl() . 'scripts/' . $file;
    }

    /**
     * Returns the templates path of the plugin.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     */
    public function getTemplatePath(string $file = ''): string
    {
        return self::getPath() . 'templates/' . $file;
    }

    /**
     * Checks if the given file exists in plugins images directory.
     *
     * @param string $file
     * @return bool
     */
    public function imageExists(string $file): bool
    {
        return $this->existFile('images', $file);
    }

    /**
     * Checks if the given file exists in plugins includes directory.
     *
     * @param string $file
     * @return bool
     */
    public function includeExists(string $file): bool
    {
        return $this->existFile('includes', $file);
    }

    /**
     * Checks if the given file exists in plugins scripts directory.
     *
     * @param string $file
     * @return bool
     */
    public function scriptExists(string $file): bool
    {
        return $this->existFile('scripts', $file);
    }

    /**
     * Checks if the given file exists in plugins styles directory.
     *
     * @param string $file
     * @return bool
     */
    public function styleExists(string $file): bool
    {
        return $this->existFile('styles', $file);
    }

    /**
     * Checks if the given file exists in plugins templates directory.
     *
     * @param string $file
     * @return bool
     */
    public function templateExists(string $file): bool
    {
        return $this->existFile('templates', $file);
    }

    /**
     * Returns the plugin information data, the record from the plugin table.
     *
     * @return \stdClass
     * @throws \cDbException
     * @throws \cInvalidArgumentException
     */
    public function getPluginInfo(): \stdClass
    {
        if (!isset($this->pluginInfo)) {
            $this->pluginInfo = new \stdClass();
            $db = \cRegistry::getDb();
            $table = \cRegistry::getDbTableName('plugins');
            $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . "\n";
            $sql = $comment
                . "SELECT * FROM `%s` WHERE `name` = '%s' AND `folder` = '%s'";
            $db->query($sql, $table, self::$name, self::$folderName);
            if ($db->nextRecord()) {
                $this->pluginInfo = $db->toObject();
            }
        }

        return $this->pluginInfo;
    }

    /**
     * Checks if a plugin file exists.
     *
     * @param string $type  Main folder within plugin folder,
     *                      e.g. 'images', 'includes', 'scripts', 'styles', etc.
     * @param string $fileName  File name
     *
     * @return bool
     */
    public function existFile(string $type, string $fileName): bool
    {
        if (empty($type) || empty($fileName)
            || strpbrk($type, "\\/?%*:|\"<>")
            || strpbrk($fileName, "?%*:|\"<>"))
        {
            // Don't allow empty file/folder or invalid folder name.
            return false;
        }

        $path = $this->getPluginPath() . $type .'/' . $fileName;
        return \cFileHandler::exists($path);
    }


    /**
     * Renders the main stylesheet link tag for the plugin.
     *
     * @return string
     */
    public function renderMainPluginStyle(): string
    {
        $href = $this->getStyleUrl(self::$folderName . '.css');
        return '
            <link rel="stylesheet" type="text/css" href="' . $href . '">
        ';
    }

    /**
     * Renders the main script tag for the plugin.
     *
     * @return string
     */
    public function renderMainPluginScript(): string
    {
        $src = $this->getScriptUrl(self::$folderName . '.js');
        return '
            <script type="text/javascript" src="' . $src . '"></script>
        ';
    }

    /**
     * Render the main stylesheet link tag and the script tag for the plugin only once.
     *
     * @return string
     */
    public function renderMainPluginStyleAndScript(): string
    {
        static $styleAndScriptRendered;

        // Render the style and the script only once
        $output = '';
        if (!isset($styleAndScriptRendered)) {
            $styleAndScriptRendered = true;
            $output = $this->renderMainPluginStyle() . $this->renderMainPluginScript();
        }

        return $output;
    }

    /**
     * Initializes (sets) and validates properties.
     *
     * @param array $properties
     */
    private function initializeProperties(array $properties)
    {
        // Set properties
        $this->properties = array_merge($this->baseProperties, $this->properties);
        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }

        if (empty($this->clientId) || !is_numeric($this->clientId)) {
            $this->clientId = \cSecurity::toInteger(\cRegistry::getClientId());
        }
        if (empty($this->languageId) || !is_numeric($this->languageId)) {
            $this->languageId = \cSecurity::toInteger(\cRegistry::getLanguageId());
        }
        if (empty($this->cfg) || !is_array($this->cfg)) {
            $this->cfg = \cRegistry::getConfig();
        }
        if (empty($this->cfgClient) || !is_array($this->cfgClient)) {
            $this->cfgClient = \cRegistry::getClientConfig();
        }
    }

}