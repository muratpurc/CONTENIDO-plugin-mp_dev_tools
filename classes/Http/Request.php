<?php

/**
 * Request.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Http;

/**
 * Request class.
 *
 * Provided access to the requests variables (`$_GET`, `$_POST`, `$_REQUEST`, etc.).
 */
class Request
{

    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * HTTP PUT array.
     * @var array
     */
    private $put = [];

    /**
     * HTTP DELETE array.
     * @var array
     */
    private $delete = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        if ($this->isPut()) {
            parse_str(file_get_contents('php://input'), $this->put);
        } elseif ($this->isDelete()) {
            parse_str(file_get_contents('php://input'), $this->delete);
        }
    }

    /**
     * Return data from GET.
     * The function returns the whole data array when the key is omitted.
     *
     * @param string|null $key The key to get the data for
     * @param mixed $default Default value to return
     * @return array|mixed|null
     */
    public function get(string $key = null, $default = null)
    {
        if ($key) {
            return $_GET[$key] ?? $default;
        } else {
            return $_GET ?? $default;
        }
    }

    /**
     * Checks if GET has a set value for a specific key.
     *
     * @param string $key The key to check
     * @return bool
     */
    public function hasGet(string $key): bool
    {
        return isset($_GET[$key]);
    }

    /**
     * Return data from POST.
     * The function returns the whole data array when the key is omitted.
     *
     * @param string|null $key The key to get the data for
     * @param mixed $default Default value to return
     * @return array|mixed|null
     */
    public function post(string $key = null, $default = null)
    {
        if ($key) {
            return $_POST[$key] ?? $default;
        } else {
            return $_POST ?? $default;
        }
    }

    /**
     * Checks if POST has a set value for a specific key.
     *
     * @param string $key The key to check
     * @return bool
     */
    public function hasPost(string $key): bool
    {
        return isset($_POST[$key]);
    }

    /**
     * Return data from GET or POST.
     * The function returns the whole data array when the key is omitted.
     * Note that POST values overwrite GET values.
     *
     * @param string|null $key The key to get the data for
     * @param mixed $default Default value to return
     * @return array|mixed|null
     */
    public function param(string $key = null, $default = null)
    {
        $params = $this->getParams();
        if ($key) {
            return $params[$key] ?? $default;
        } else {
            return $params ?? $default;
        }
    }

    /**
     * Checks if GET or POST has a set value for a specific key.
     *
     * @param string $key The key to check
     * @return bool
     */
    public function hasParam(string $key): bool
    {
        $params = $this->getParams();
        return isset($params[$key]);
    }

    /**
     * Return data from REQUEST.
     * The function returns the whole data array when the key is omitted.
     *
     * @param string|null $key The key to get the data for
     * @param mixed $default Default value to return
     * @return array|mixed|null
     */
    public function request(string $key = null, $default = null)
    {
        if ($key) {
            return $_REQUEST[$key] ?? $default;
        } else {
            return $_REQUEST ?? $default;
        }
    }

    /**
     * Checks if REQUEST has a set value for a specific key.
     *
     * @param string $key The key to check
     * @return bool
     */
    public function hasRequest(string $key): bool
    {
        return isset($_REQUEST[$key]);
    }

    /**
     * Return data from PUT.
     * The function returns the whole data array when the key is omitted.
     *
     * @param string|null $key The key to get the data for
     * @param mixed $default Default value to return
     * @return array|mixed|null
     */
    public function put(string $key = null, $default = null)
    {
        if ($key) {
            return $this->put[$key] ?? $default;
        } else {
            return $this->put ?? $default;
        }
    }

    /**
     * Checks if PUT has a set value for a specific key.
     *
     * @param string $key The key to check
     * @return bool
     */
    public function hasPut(string $key): bool
    {
        return isset($this->put[$key]);
    }

    /**
     * Return data from DELETE.
     * The function returns the whole data array when the key is omitted.
     *
     * @param string|null $key The key to get the data for
     * @param mixed $default Default value to return
     * @return array|mixed|null
     */
    public function delete(string $key = null, $default = null)
    {
        if ($key) {
            return $this->delete[$key] ?? $default;
        } else {
            return $this->delete ?? $default;
        }
    }

    /**
     * Checks if DELETE has a set value for a specific key.
     *
     * @param string $key The key to check
     * @return bool
     */
    public function hasDelete(string $key): bool
    {
        return isset($this->delete[$key]);
    }

    /**
     * Return data from COOKIE.
     * The function returns the whole data array when the key is omitted.
     *
     * @param string|null $key The key to get the data for
     * @param mixed $default Default value to return
     * @return array|mixed|null
     */
    public function cookie(string $key = null, $default = null)
    {
        if ($key) {
            return $_COOKIE[$key] ?? $default;
        } else {
            return $_COOKIE ?? $default;
        }
    }

    /**
     * Checks if COOKIE has a set value for a specific key.
     *
     * @param string $key The key to check
     * @return bool
     */
    public function hasCookie(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * Returns the request method.
     *
     * @return string
     */
    public function method()
    {
        return $this->server('REQUEST_METHOD', '');
    }

    /**
     * Checks if it is an AJAX request.
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        if (strtolower($this->server('X_REQUESTED_WITH', '')) === 'xmlhttprequest') {
            return true;
        } elseif ($this->param('ajax')) {
            return true;
        }

        return false;
    }

    /**
     * Checks if it is an HTTP DELETE request.
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->method() === self::METHOD_DELETE;
    }

    /**
     * Checks if it is an HTTP GET request.
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() === self::METHOD_GET;
    }

    /**
     * Checks if it is an HTTP HEAD request.
     *
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->method() === self::METHOD_HEAD;
    }

    /**
     * Checks if it is an HTTP OPTIONS request.
     *
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->method() === self::METHOD_OPTIONS;
    }

    /**
     * Checks if it is an HTTP PATCH request.
     *
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->method() === self::METHOD_PATCH;
    }

    /**
     * Checks if it is an HTTP POST request.
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() === self::METHOD_POST;
    }

    /**
     * Checks if it is an HTTP PUT request.
     *
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->method() === self::METHOD_PUT;
    }

    /**
     * Merges GET and POST parameters and returns the result.
     *
     * @return array
     */
    private function getParams(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Returns
     * @param $key
     * @param $default
     * @return array|mixed|null
     */
    private function server($key = null, $default = null)
    {
        if ($key) {
            return $_SERVER[$key] ?? $default;
        } else {
            return $_SERVER ?? $default;
        }
    }

}