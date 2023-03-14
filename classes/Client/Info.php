<?php

/**
 * Client info.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Client;

/**
 * Client info.
 *
 * Wrapper for `$cfgClient[$client]` array.
 * Provides easy access to the client configuration array.
 */
class Info
{

    /**
     * @var int
     */
    private $clientId;

    /**
     * @var array
     */
    private $clientCfg;

    /**
     * Constructor.
     *
     * @param int $clientId Id of client. If no id is set, the current client
     *     known to the system will be used.
     * @throws \cException
     */
    public function __construct(int $clientId = 0)
    {
        if ($clientId === 0) {
            $clientId = \cSecurity::toInteger(\cRegistry::getClientId());
        }
        $cfgClient = \cRegistry::getClientConfig();
        if (!isset($cfgClient[$clientId]) || !is_array($cfgClient[$clientId]) || count($cfgClient[$clientId]) === 0) {
            throw new \cException('Could not initialize client configuration!');
        }

        $this->clientId = $clientId;
        $this->clientCfg = $cfgClient[$clientId];
    }

    public function getId(): int
    {
        return $this->clientId;
    }

    public function getName(): string
    {
        return $this->clientCfg['name'] ?? '';
    }

    public function getErrorSiteCategoryId(): int
    {
        return \cSecurity::toInteger($this->clientCfg['errsite']['idcat'] ?? '0');
    }

    public function getErrorSiteArticleId(): int
    {
        return \cSecurity::toInteger($this->clientCfg['errsite']['idart'] ?? '0');
    }

    public function getPath(): string
    {
        return $this->clientCfg['path']['frontend'] ?? '';
    }

    public function getUrl(): string
    {
        return $this->clientCfg['path']['htmlpath'] ?? '';
    }

    public function getImagePath(string $file = ''): string
    {
        return $this->getPath() . $this->getImagesFolder() . $file;
    }

    public function getImageUrl(string $file = ''): string
    {
        return ($this->clientCfg['images'] ?? '') . $file;
    }

    public function getUploadPath(string $file = ''): string
    {
        return ($this->clientCfg['upl']['path'] ?? '') . $file;
    }

    public function getUploadUrl(string $file = ''): string
    {
        return ($this->clientCfg['upl']['htmlpath'] ?? '') . $file;
    }

    public function getCssPath(string $file = ''): string
    {
        return ($this->clientCfg['css']['path'] ?? '') . $file;
    }

    public function getCssUrl(string $file = ''): string
    {
        return $this->pathToUrl($this->getCssPath()) . $file;
    }

    public function getJsPath(string $file = ''): string
    {
        return ($this->clientCfg['js']['path'] ?? '') . $file;
    }

    public function getJsUrl(string $file = ''): string
    {
        return $this->pathToUrl($this->getJsPath()) . $file;
    }

    public function getTemplatePath(string $file = ''): string
    {
        return ($this->clientCfg['tpl']['path'] ?? '') . $file;
    }

    public function getCachePath(string $file = ''): string
    {
        return ($this->clientCfg['cache']['path'] ?? '') . $file;
    }

    public function getCodePath(string $file = ''): string
    {
        return ($this->clientCfg['code']['path'] ?? '') . $file;
    }

    public function getXmlPath(string $file = ''): string
    {
        return ($this->clientCfg['xml']['path'] ?? '') . $file;
    }

    public function getDataPath(string $file = ''): string
    {
        return ($this->clientCfg['data']['path'] ?? '') . $file;
    }

    public function getModulePath(string $file = ''): string
    {
        return ($this->clientCfg['module']['path'] ?? '') . $file;
    }

    public function getConfigPath(string $file = ''): string
    {
        return ($this->clientCfg['config']['path'] ?? '') . $file;
    }

    public function getLayoutPath(string $file = ''): string
    {
        return ($this->clientCfg['layout']['path'] ?? '') . $file;
    }

    public function getLogPath(string $file = ''): string
    {
        return ($this->clientCfg['log']['path'] ?? '') . $file;
    }

    public function getVersionPath(string $file = ''): string
    {
        return ($this->clientCfg['version']['path'] ?? '') . $file;
    }

    /**
     * Removes the frontend path from passed path and returns a path
     * starting from frontend directory.
     *
     * @param string $path
     * @return string
     */
    public function relativePath(string $path): string
    {
        return str_replace($this->getPath(), '', $path);
    }

    /**
     * Removes the frontend html path from passed URL and returns a
     * html path starting from frontend.
     *
     * @param string $url
     * @return string
     */
    public function relativeUrl(string $url): string
    {
        return str_replace($this->getUrl(), '', $url);
    }

    private function pathToUrl(string $path): string
    {
        return str_replace($this->getPath(), $this->getUrl(), $path);
    }

    private function getImagesFolder(): string
    {
        if (!isset($this->imagesFolder)) {
            $parts = \cUri::getInstance()->parse(trim($this->getImageUrl(), '/'));
            if (isset($parts['path'])) {
                $path = explode('/', $parts['path']);
                $this->imagesFolder = $path[count($path) - 1];
            } else {
                $this->imagesFolder = '';
            }
            if (!empty($this->imagesFolder)) {
                $this->imagesFolder .= '/';
            }
        }

        return $this->imagesFolder;
    }

}