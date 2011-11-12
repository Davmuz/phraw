<?php
/**
 * Phraw - PHP mini-framework
 *
 * Framework instance.
 * 
 * @copyright Copyright (C) 2010-2011 Davide Muzzarelli <d.muzzarelli@dav-muz.net>. All rights reserved.
 * @license BSD, see LICENSE.txt for more details.
 * @version dev
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
     * Contains the URI values extracted from the url.
     *
     * @var array
     */
    public $uri_values;
    
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
    public $version = 'dev';
    
    /**
     * Constructor.
     *
     * @param string $uri_get_key The name of the GET parameter that contains the URI.
     */
    function __construct($uri_get_key=null) {
        $this->uri = $this->get_uri($uri_get_key);
    }
    
    /**
     * Add the path to the include path.
     *
     * @param string $include_path Path to add to the include path.
     * @param bool $append If true append the path, if false prepend it.
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
     * Returns the URI of the current request.
     *
     * @param string $get_key The name of the GET parameter that contains the URI.
     * @return string URI string.
     */
    static function get_uri($get_key=null) {
        if ($get_key) {
            return isset($_GET[$get_key]) ? ltrim($_GET[$get_key], '/') : '';
        }
        if (!empty($_SERVER['PATH_INFO'])) {
            return ltrim($_SERVER['PATH_INFO'], '/');
        }
        return ltrim(isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : $_SERVER['PHP_SELF'], '/');
    }
    
    /**
     * URI matching.
     * The matching values are stored in $this->uri_values.
     * The route mechanism can use a built-in function or a custom function passed by name.
     *
     * Built-in methods:
     * - rexp: uses regular expressions (default).
     * - prexp: uses regular expressions only in parentheses.
     * - equal: matches equal strings.
     *
     * Using a custom function: create a new function or objcect method with the parameters &$uri and &$uri_values. See the documentation for more informations.
     *
     * @param string $uri URI to match.
     * @param mixed $function Method name (string) used to match the URI or function name (string) or method name (array) for a custom function call.
     * @return bool True if the url is matched.
     */
    function route($uri, $function='rexp') {
        switch ($function) {
            case 'equal': # Simple equal strings match
                if ($uri === $this->uri) {
                    $this->uri_values = array($this->uri);
                    return true;
                }
                return false;
            case 'prexp': # Regular expressions only on parentheses
                # Escape
                $chunks = preg_split('/(\()|(\))/', $uri, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $regx_uri = '';
                $inside_parentheses = false;
                foreach ($chunks as $chunk) {
                    if ($chunk == '(') {
                        $inside_parentheses = true;
                    } else if ($chunk == ')') {
                        $inside_parentheses = false;
                    } else if (!$inside_parentheses) {
                        $chunk = preg_quote($chunk, '/');
                    }
                    $regx_uri .= $chunk;
                }
                
                # Replace rexp start and end
                $uri_len = strlen($uri);
                if ($uri_len && $uri[0] === '^') {
                    $regx_uri = substr($regx_uri, 1);
                }
                if ($uri_len && $uri[$uri_len - 1] === '$') {
                    $regx_uri = substr($regx_uri, 0, -2) . '$';
                }
                
                return preg_match('/' . $regx_uri . '/', $this->uri, $this->uri_values);
            case 'rexp': # Regular expression
                return preg_match('/' . $uri . '/', $this->uri, $this->uri_values);
            default: # Function or method call
                return call_user_func_array($function, array(&$uri, &$this->uri, &$this->uri_values));
        }
    }
    
    /**
     * URI matching for an array of pages.
     *
     * @param array $uri_list Key = regex path, Value = page path.
     * @param string $assign Variable where store the custom values of the matched URI.
     * @param mixed $function Method used to match the URI See the $function parameter of the route() method.
     * @return mixed Array item value or true (if assing===false) (DEPRECATED). Returns true if something is matched.
     */
    function bulk_route(&$uri_list, &$assign, $function='rexp') {
        foreach ($uri_list as $uri => $value) {
            if ($this->route($uri, $function)) {
                $assign = $value;
                return true;
            }
        }
        return false;
    }
    
    /**
     * URI matching for an tree of pages.
     * URI is build following the tree. The values, if in an array, are merged upside.
     *
     * @param array $uri_tree An array of arrays. The key is the rexp path. The value is an array where the first one contains the continue of the tree or null, the following values contains the custom values.
     * @param string $assign Variable where store the custom values of the matched URI.
     * @param mixed $function Method used to match the URI See the $function parameter of the route() method.
     * @param string $_prefix Private variable for recursion. Don't use it.
     * @return bool True if something is matched.
     */
    function tree_route(&$uri_tree, &$assign, $function='rexp', $_prefix='') {
        foreach ($uri_tree as $partial_uri => $value) {
            if ($value[0]) { # It's a branch
                $result = $this->tree_route($value[0], $assign, $function, $partial_uri);
                if ($result) {
                    if (is_array($assign)) {
                        $assign = array_merge(array_slice($value, 1), $assign);
                    }
                    return $result;
                }
            } else { # It's a leaf
                if ($this->route($_prefix . $partial_uri, $function)) {
                    $assign = is_array($assign) ? array_merge($assign, array_slice($value, 1)) : array_slice($value, 1);
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
        return $this->uri ? substr($this->uri, -1) !== '/' : false;
    }
    
    /**
     * Return the current domain with the protocol used.
     *
     * @return string Protocol and domain name. Eg. http://www.mysite.com
     */
    static function get_current_domain() {
        return (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
    }
    
    /**
     * Fix the URI adding the trailing slash.
     * It does a permanent redirect to the correct URL.
     */
    function fix_trailing_slash() {
        $url = $this->get_current_domain() . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] . '/' : '/');
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