<?php
/**
 * Sessions extension.
 *
 * @copyright Copyright (C) 2010-2011 Davide Muzzarelli <d.muzzarelli@dav-muz.net>. All rights reserved.
 * @license BSD, see LICENSE.txt for more details.
 */

/**
 * Session handler base class.
 * Extend this class with a concrete implementation of session functions.
 * Read the official documentation for more informations: http://www.php.net/manual/en/function.session-set-save-handler.php
 */ 
abstract class SessionSaveHandler {
    /**
     * It is possible to extend this class and pass custom parameters.
     */
    public function __construct() {
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
    }
    
    /**
     * Is executed when the session is being opened.
     *
     * @param string $save_path Save path of the session.
     * @param string $session_name Session name.
     */
    abstract public function open($save_path, $session_name);
    
    /**
     * Is executed when the session operation is done.
     */
    abstract public function close();
    
    /**
     * Must return string value always to make save handler work as expected.
     *
     * @param string $session_id Sesion identifier.
     * @return string Session data. Return a void string if there is no data to read.
     */
    abstract public function read($session_id);
    
    /**
     * Is called when session data is to be saved.
     *
     * @param string $session_id Sesion identifier.
     * @param mixed $session_data Session data.
     */
    abstract public function write($session_id, $session_data);
    
    /**
     * Is executed when a session is destroyed with session_destroy().
     *
     * @param string $session_id Sesion identifier.
     */
    abstract public function destroy($session_id);
    
    /**
     * Is executed when the session garbage collector is executed.
     * This function should remove the old session data, older than the max life time.
     *
     * @param int $max_life_time Maximum life time of session data to remove.
     */
    abstract public function gc($max_life_time);
}
?>