<?php
/**
 * Phraw - PHP mini-framework
 *
 * Framework instance.
 * 
 * @copyright Copyright (C) 2010-2011 Davide Muzzarelli <d.muzzarelli@dav-muz.net>. All rights reserved.
 * @license BSD, see LICENSE.txt for more details.
 * @version 0.3
 * @package phraw
 */

# Error reporting
if (!defined('DEBUG')) {
    define('DEBUG', false);
}
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors','Off');
}

# Resources directory path
if (!defined('RESOURCES_DIR')) {
    define('RESOURCES_DIR', 'resources');
}

/**
 * Phraw
 *
 * Main class.
 *
 * @package Phraw
 */ 
class Phraw {
    /**
     * Contains the requested URI.
     *
     * @var string
     */
    public $uri;
    
    /**
     * Contains the regex values matched from the url.
     *
     * @var array
     */
    public $request;
    
    /**
     * Session handler object.
     *
     * @var SessionSaveHandler
     */
    public $session_handler;

    /**
      * Version.
      *
      * @ver string
      */
    public $version = '0.3';
    
    /**
     * Constructor. Get the requested URI.
     *
     * @param string $uri_key Optional: the name of the GET variable key that contain the URI.
     */
    function __construct($uri_key=null) {
        // Get the URI
        if ($uri_key) {
            $this->uri = (isset($_GET[$uri_key])) ? '/' . $_GET[$uri_key] : '/';
        } else {
            $this->uri = $this->get_uri();
        }
    }
    
    /**
     * Add the path to the include path.
     *
     * @param string Path to add to the include path.
     * @param bool If true append it to the end.
     */
    static function add_include_path($include_path, $append=false) {
        if ($append) {
            set_include_path($include_path . PATH_SEPARATOR . get_include_path());
        } else {
            set_include_path(get_include_path() . PATH_SEPARATOR . $include_path);
        }
    }

    /**
     * Get the URI of the current request.
     *
     * @return string URI string.
     */
    static function get_uri() {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
    }
    
    /**
     * URI matching for regex.
     * The matching values are stored in $this->request.
     *
     * Methods:
     * - null: uses regular expressions.
     * - equal: matches equal strings.
     *
     * @param string $uri URI to match.
     * @param bool $simple Method used to match the URI. Default: uses regular expressions.
     * @return bool True if the url is matched.
     */
    function route($uri, $method=null) {
        switch ($method) {
            case 'equal': # Simple equal strings match
                if ('/' . $uri == $this->uri) {
                    $this->request = array($this->uri);
                    return true;
                }
                return false;
                break;
            default: # Regular expression
                return preg_match('/^\/' . $uri . '$/', $this->uri, $this->request);
        }
    }
    
    /**
     * URI matching for an array of pages.
     *
     * @param array $uris Key = regex path, Value = page path.
     * @param variable $assign Place the value of the array in the given variable
     * @return string Method used to match the URI.
     */
    function bulk_route(&$uri_list, &$assign=false, $method=null) {
        foreach ($uri_list as $uri => $value) {
            if ($this->route($uri, $method)) {
                if ($assign === false) {
                    return $value;
                } else {
                    $assign = $value;
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * HTTP redirection.
     * It is possibile to display content in the redirect page.
     *
     * @param string $url URL to redirect. Use null for not add the "Location" header.
     * @param int $type Type of redirection.
     */
    static function redirect($url=null, $type=301) {
        $codes = array(
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect'
        );
        header('HTTP/1.1 ' . $type . ' ' . $codes[$type]);
        if ($url) {
            header('Location: ' . $url);
        }
    }
    
    /**
     * Start a session using the given SessionSaveHandler base class.
     *
     * @param SessionSaveHandler $class SessionSaveHandler extended class.
     * @param mixin Variable arguments for the handler class.
     */
    function session_start($class /* variable arguments */) {
        $reflection = new ReflectionClass($class);
        $this->session_handler = $reflection->newInstanceArgs(array_slice(func_get_args(), 1));
        session_start();
    }
    
    /**
     * Detect the absence of the trailing slash.
     *
     * @return bool True if not present or False if present.
     */
    function detect_no_trailing_slash() {
        return $this->uri ? substr($this->uri, -1) != '/' : false;
    }
    
    /**
     * Return the current domain and the protocol used.
     *
     * @return string Protocol and domain name. Eg. http://www.mysite.com
     */
    static function get_current_domain() {
        return (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
    }
    
    /**
     * Fix the URI adding the trailing slash.
     * Do a permanent redirect to the correct URL.
     */
    function fix_trailing_slash() {
        $url = $this->get_current_domain() . '/';
        if (strpos($_SERVER['REQUEST_URI'], '?') == false) {
            # There are not GET variables, this is the simple case
            $url .= ltrim($this->uri . '/', '/');
        } else {
            # There are GET variables, add the trailing slash before the first "?"
            $url .= ltrim(substr_replace($_SERVER['REQUEST_URI'], '/', strpos($_SERVER['REQUEST_URI'], '?'), 0), '/');
        }
        $this->redirect($url);
    }
    
    /**
     * Set a HTTP client error 4xx header.
     *
     * @param int $type Type of client error.
     */
    static function client_error($type=404) {
        $codes = array(
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            400 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot', # April Fools' jokes
            422 => 'Unprocessable Entity', # WEBDAV
            423 => 'Locked', # WEBDAV
            424 => 'Failed Dependency', # WEBDAV
            425 => 'Unordered Collection', # See RFC 3648
            426 => 'Upgrade Required', # See RFC 2817
            444 => 'No Response', # Nginx extension
            449 => 'Retry With', # Microsoft extension
            450 => 'Blocked by Windows Parental Controls', # Microsoft extension
            499 => 'Client Closed Request' # Nginx extension
        );
        header('HTTP/1.1 ' . $type . ' ' . $codes[$type]);
    }

    /**
     * Set a HTTP success 2xx header.
     *
     * @param int $type Type of success message.
     */
    static function success_header($type=200) {
        $codes = array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status' # WEBDAV
        );
        header('HTTP/1.1 ' . $type . ' ' . $codes[$type]);
    }
}
?>