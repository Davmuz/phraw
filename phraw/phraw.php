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
     */
    function __construct($url_key='u') {
        $this->url = (isset($_GET[$url_key])) ? $_GET[$url_key] : ''; // Get the query string
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
     * HTTP redirection.
     * It is possibile to diplay content in the redirect page.
     *
     * @param string $url URL to redirect.
     * @param bool $content An optional HTML page.
     * @param int $type Type of redirection.
     */
    function redirect($url, $type=301) {
        switch ($type) {
            case 301:
                # Permanent redirect
                header('HTTP/1.1 301 Moved Permanently');
                break;
            case 302:
                # Temporary redirect
                header('HTTP/1.1 302 Found');
                break;
        }
        header('Location: ' . $url);
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
     * Fix the URL adding the trailing slash.
     * Do a permanent redirect to the correct URL.
     */
    function fix_trailing_slash() {
        $url = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'] . '/';
        if (strpos($_SERVER['REQUEST_URI'], '?') == false) {
            # There are not GET variables, this is the simple case
            $url .= ltrim($this->url . '/', '/');
        } else {
            # There are GET variables, add the trailing slash before the first "?"
            $url .= ltrim(substr_replace($_SERVER['REQUEST_URI'], '/', strpos($_SERVER['REQUEST_URI'], '?'), 0), '/');
        }
        $this->redirect($url);
    }

}

/**
 * The default starter shortcut.
 */ 
class DefaultStarter {
    /**
     * Smarty instance.
     *
     * @var Smarty
     */
    public $template_engine;
    
    /**
     * Phraw instance.
     *
     * @var Phraw
     */
    public $phraw;
    
    /**
     * Constructor. Initialize Phraw and Smarty.
     * @param string $lib_dir If true, insert the path in the include paths.
     * @param string $url_key See $url_key constructor variable of the Phraw class.
     */
    function __construct($lib_dir='./lib', $url_key=null) {
        # Add the "lib" directory to the include paths
        if ($lib_dir) {
            set_include_path($lib_dir . PATH_SEPARATOR . get_include_path());
        }
        # Load Phraw
        $this->phraw = ($url_key != null) ? new Phraw($url_key) : new Phraw();
        # Load Smarty
        require_once('phraw/extensions/smarty.php');
        $this->template_engine = new SmartyTemplateEngine();
    }
    
    /**
     * Url matching for an array of static pages.
     *
     * @param array $urls Key = regex path, Value = page path.
     * @return string|bool The page path or false if not matched.
     */
    function static_route(&$urls) {
        $page = static_route($urls, $this->phraw, $this->template_engine);
        if ($page) {
            $this->template_engine->display($page);
            return $page;
        }
        return $page;
    }
    
    /**
     * Display a 404 error (page not found).
     */
    function display_error_404() {
        $this->template_engine->display_error_404();
    }
}

/**
 * Url matching for an array of static pages.
 *
 * @param Phraw $phraw A Phraw insance.
 * @param array $urls Key = regex path, Value = page path.
 * @return string|bool The page path or false if not matched.
 */
function static_route(&$urls, $phraw) {
    foreach ($urls as $url => $page) {
        if ($phraw->route($url)) {
            return $page;
        }
    }
    return false;
}
?>