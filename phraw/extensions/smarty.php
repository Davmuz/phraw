<?php
/**
 * Smarty extension.
 *
 * The Smarty template engine can be found here: http://www.smarty.net/
 *
 * @copyright Copyright (C) 2010-2011 Davide Muzzarelli <d.muzzarelli@dav-muz.net>. All rights reserved.
 * @license BSD, see LICENSE.txt for more details.
 */
 
require_once('smarty/Smarty.class.php');

/**
 * Smarty, the default template engine extension for Phraw.
 */ 
class SmartyTemplateEngine extends Smarty {

    /**
     * Constructor. Set the working directories. Disable some features if the debug mode is active.
     *
     * @param int $caching Activate the template caching.
     */
    function __construct($caching=1) {
        parent::__construct();
        $this->template_dir = RESOURCES_DIR . '/templates/';
        $this->compile_dir = RESOURCES_DIR . '/compiled/';
        $this->cache_dir = RESOURCES_DIR . '/cached/';
        $this->caching = $caching;
        
        if (DEBUG) {
            $this->force_compile = true;
            $this->caching = 0;
        }
    }
    
    /**
     * Display a client error page.
     * 
     * @param int $type Type of message. Default: 404 Page Not Found.
     */
    function display_error($type=404) {
        Phraw::client_error($type);
        $this->display($type . '.html');
    }
}
?>