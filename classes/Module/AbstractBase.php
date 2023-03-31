<?php

/**
 * Base module class.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Module;

use CONTENIDO\Plugin\MpDevTools\Http\Request;
use CONTENIDO\Plugin\MpDevTools\Traits\BackendHelper;
use CONTENIDO\Plugin\MpDevTools\Traits\ClientInfo;
use CONTENIDO\Plugin\MpDevTools\Traits\Gui;
use CONTENIDO\Plugin\MpDevTools\Traits\Properties;
use CONTENIDO\Plugin\MpDevTools\Traits\Settings;

/**
 * Base module class.
 *
 * Provides common functionality which can be derived and used by other module classes.
 *
 * @property int $moduleId Module id (aka `$cCurrentModule` or `$GLOBALS['cCurrentModule']`),
 *      will be set from globals by default.
 * @property int $containerNumber Module id (aka `$cCurrentContainer` or `$GLOBALS['cCurrentContainer']`),
 *      will be set from globals by default.

 * @property array $cfg Global configuration array, will be set from globals by default.
 * @property array $cfgClient Clients configuration array, will be set from globals by default.
 * @property int $articleId Article id, will be set from globals by default.
 * @property int $categoryId Category id, will be set from globals by default.
 * @property int $clientId  Client id, will be set from globals by default.
 * @property int $languageId Language id, will be set from globals by default.
 * @property bool $debug Flag for debugging, `false` by default.
 */
abstract class AbstractBase
{

    use BackendHelper;
    use ClientInfo;
    use Gui;
    use Properties;
    use Settings;

    /**
     * Base module properties structure.
     *
     * @var  array
     */
    private $baseProperties = [
        'moduleId' => 0,
        'containerNumber' => 0,

        'cfg' => [],
        'cfgClient' => [],
        'articleId' => 0,
        'categoryId' => 0,
        'clientId' => 0,
        'languageId' => 0,
        'debug' => false,
    ];

    /**
     * @inheritdoc
     * @var array|mixed
     */
    protected $properties = [];

    /**
     * Module handler instance.
     *
     * @var \cModuleHandler
     */
    private $moduleHandler;

    /**
     * HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    private $name;

    /**
     * Module identifier, composition of module id and container number,
     * the format is `<moduleId>_<containerNumber>`.
     *
     * @var string
     */
    private $identifier;

    /**
     * Constructor, sets some properties.
     *
     * @param string $name Module name. Should be ideally the module name, known by the system.
     *      We don't want to retrieve it from the module handler, since it queries the db.
     * @param array $properties Associative properties array
     *      {@see AbstractBase::$properties} or properties.
     *
     * @throws \cException
     */
    public function __construct(string $name, array $properties)
    {
        $this->name = $name;

        $this->initializeProperties($properties);

        if (empty($this->moduleId) || empty($this->containerNumber)) {
            throw new \cException('Missing `moduleId` and/ or `containerNumber`!');
        }
        if (empty($this->name)) {
            throw new \cException('Module name is empty!');
        }

        $this->identifier = $this->moduleId . '_' . $this->containerNumber;

        $this->setSettingsNamespace('module_' . $this->name);

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
     * Returns the module identifier, a composition of module id and container number.
     * If a key is passed, then it will return the identifier prefixed with the key.
     *
     * Return formats are:
     * - `<moduleId>_<containerNumber>`
     * - `<key>_<moduleId>_<containerNumber>`
     *
     * @param string $key Should be an alphanumeric value, containing additionally
     *      the characters '_', '-', and '.'.
     * @return string Module identifier, optionally prefixed with the passed key.
     *      The return value can be used in id-attributes of HTML elements.
     */
    public function getIdentifier(string $key = ''): string
    {
        $prefix = !empty($key) ? $key . '_' : '';
        return $prefix . $this->identifier;
    }

    /**
     * Returns the module handler by lazy initialization.
     *
     * @return \cModuleHandler
     * @throws \cException
     */
    protected function getModuleHandler(): \cModuleHandler
    {
        if (!isset($this->moduleHandler)) {
            $this->moduleHandler = new \cModuleHandler($this->moduleId);
        }
        return $this->moduleHandler;
    }

    /**
     * Get the name of module.
     *
     * @return string
     * @throws \cException
     */
    public function getModuleName(): string
    {
        return $this->getModuleHandler()->getModuleName();
    }

    /**
     * Get the module Path also cms path + module + module name.
     *
     * @return string
     * @throws \cException
     */
    public function getModulePath(): string
    {
        return $this->getModuleHandler()->getModulePath();
    }

    /**
     * Returns the css path of the module.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     * @throws \cException
     */
    public function getCssPath(string $file = ''): string
    {
        return $this->getModuleHandler()->getCssPath() . $file;
    }

    /**
     * Returns the js path of the module.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     * @throws \cException
     */
    public function getJsPath(string $file = ''): string
    {
        return $this->getModuleHandler()->getJsPath() . $file;
    }

    /**
     * Returns the php path of the module.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     * @throws \cException
     */
    public function getPhpPath(string $file = ''): string
    {
        return $this->getModuleHandler()->getPhpPath() . $file;
    }

    /**
     * Checks if the given file exists in modules template directory.
     *
     * @param string $file
     * @return bool
     * @throws \cException
     */
    public function templateExists(string $file): bool
    {
        if (empty($file)) {
            return false;
        }
        return $this->getModuleHandler()->existFile('template', $file);
    }

    /**
     * Returns the template path of the module.
     * If a file is set, it will return the path to the file.
     *
     * @param string $file
     * @return string
     * @throws \cException
     */
    public function getTemplatePath(string $file = ''): string
    {
        return $this->getModuleHandler()->getTemplatePath($file);
    }

    /**
     * Returns the CmsToken instance for the desired module CMS_VAR and CMS_VALUE token.
     *
     * @param int $index Token index.
     * @param string $type The token variable type ('string', 'int', or 'bool')
     * @return CmsToken
     */
    public function getCmsToken(int $index, string $type = CmsToken::TYPE_STRING): CmsToken
    {
        return new CmsToken($this->containerNumber, $index, $type);
    }

    /**
     * Initializes (sets) and validates properties.
     *
     * @param array $properties
     */
    private function initializeProperties(array $properties)
    {
        $this->properties = array_merge($this->properties, $this->baseProperties);

        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }

        if (empty($this->moduleId) || !is_numeric($this->moduleId)) {
            $this->moduleId = \cSecurity::toInteger($GLOBALS['cCurrentModule'] ?? '0');
        }
        if (empty($this->containerNumber) || !is_numeric($this->containerNumber)) {
            $this->containerNumber = \cSecurity::toInteger($GLOBALS['cCurrentContainer'] ?? '0');
        }

        if (empty($this->clientId) || !is_numeric($this->clientId)) {
            $this->clientId = \cSecurity::toInteger(\cRegistry::getClientId());
        }
        if (empty($this->languageId) || !is_numeric($this->languageId)) {
            $this->languageId = \cSecurity::toInteger(\cRegistry::getLanguageId());
        }
        if (empty($this->articleId) || !is_numeric($this->articleId)) {
            $this->articleId = \cSecurity::toInteger(\cRegistry::getArticleId());
        }
        if (empty($this->categoryId) || !is_numeric($this->categoryId)) {
            $this->categoryId = \cSecurity::toInteger(\cRegistry::getCategoryId());
        }
        if (empty($this->cfg) || !is_array($this->cfg)) {
            $this->cfg = \cRegistry::getConfig();
        }
        if (empty($this->cfgClient) || !is_array($this->cfgClient)) {
            $this->cfgClient = \cRegistry::getClientConfig();
        }
    }

}