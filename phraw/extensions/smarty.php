<?php
/**
 * Smarty extension.
 * 
 * @copyright Copyright (C) 2010-2011 Davide Muzzarelli <davide@davideweb.com>. All rights reserved.
 * @license BSD, see LICENSE.txt for more details.
 */
 
require_once('smarty/Smarty.class.php');

/**
 * Smarty, the default template engine for Phraw.
 */ 
class SmartyTemplateEngine extends Smarty {

    /**
     * Constructor. Set the working directories. Disable some features if the debug mode is active.
     *
     * @param bool $caching Activate the template caching.
     */
    function __construct($caching=true) {
        parent::__construct();
        $this->template_dir = RESOURCES_DIR . '/templates/';
        $this->compile_dir = RESOURCES_DIR . '/compiled/';
        $this->cache_dir = RESOURCES_DIR . '/cached/';
        $this->caching = $caching;
        
        if (DEBUG) {
            $this->force_compile = true;
            $this->caching = false;
        }
    }
    
    /**
     * Display a 404 error (page not found).
     */
    function display_error_404() {
        header('HTTP/1.x 404 Not Found');
        $this->display('404.html');
    }
}
?>