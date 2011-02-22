<?php
/**
 * Phraw - PHP mini-framework
 *
 * Framework instance.
 * 
 * @copyright Copyright (C) 2010-2011 Davide Muzzarelli <davide@davideweb.com>. All rights reserved.
 * @license BSD, see LICENSE.txt for more details.
 * @version 0.2
 * @package Phraw
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
     * Contains the requested url.
     *
     * @var string
     */
    public $url;
    
    /**
     * Contains the regex values matched from the url.
     *
     * @var array
     */
    public $request;

    /**
      * Version.
      * @ver string
      */
    public $version = '0.2';
    
    /**
     * Constructor. Get the requested url.
     * @param string $url_key The name of the GET variable key that contain the url.
     * @param string $include_path Add the directory path in the include paths.
     */
    function __construct($url_key='u') {
        $this->url = (isset($_GET[$url_key])) ? $_GET[$url_key] : ''; // Get the query string
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
     * Url matching for regex.
     * The matching values are stored in $this->request.
     *
     * @param string $url Regex of the url to match.
     * @param bool $simple If true it add the standard head and tail to the regex string.
     * @return bool True if the url is matched.
     */
    function route($url, $simple=true) {
        if($simple) {
            $url = '/^\/?' . $url . '\/?$/';
        }
        return preg_match($url, $this->url, $this->request);
    }
    
    /**
     * Url matching for an array of pages.
     *
     * @param array $urls Key = regex path, Value = page path.
     * @param variable $assign Place the value of the array in the given variable
     * @return mixin|bool The value matched or true if $assign is used. False if not matched.
     */
    function bulk_route(&$urls, &$assign=false, $simple=true) {
        foreach ($urls as $url => $value) {
            if ($this->route($url, $simple)) {
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
     * It is possibile to diplay content in the redirect page.
     *
     * @param string $url URL to redirect. Use null for not add the "Location" header.
     * @param int $type Type of redirection.
     */
    function redirect($url=null, $type=301) {
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
     * Detect the absence of the trailing slash.
     *
     * @return bool True if not present or False if present.
     */
    function detect_no_trailing_slash() {
        if ($this->url) {
            return substr($this->url, -1) != '/';
        } else {
            return false;
        }
    }
    
    /**
     * Return the current domain and the protocol used.
     *
     * @return string Protocol and domain name. Eg. http://www.mysite.com
     */
    function get_current_domain() {
        $url = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        return $url;
    }
    
    /**
     * Fix the URL adding the trailing slash.
     * Do a permanent redirect to the correct URL.
     */
    function fix_trailing_slash() {
        $url = $this->get_current_domain() . '/';
        if (strpos($_SERVER['REQUEST_URI'], '?') == false) {
            # There are not GET variables, this is the simple case
            $url .= ltrim($this->url . '/', '/');
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
    static function client_error($type=400) {
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
    static function success_header($type=400) {
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