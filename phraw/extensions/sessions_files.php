<?php
/**
 * Sessions files handler extension.
 *
 * This class is more secure than the default session handling behavior.
 * It is possible to encrypt the session files giving an encrypt object.
 *
 * @copyright Copyright (C) 2010-2011 Davide Muzzarelli <d.muzzarelli@dav-muz.net>. All rights reserved.
 * @license BSD, see LICENSE.txt for more details.
 */
 
require_once('phraw/extensions/sessions.php');

/**
 * Session handler with files.
 */ 
class SessionFilesHandler extends SessionSaveHandler {
    /**
     * Session files directory path.
     * The given directory must be writable by PHP.
     *
     * @var string
     */
    public $save_path;
    
    /**
     * Session file name prefix.
     *
     * @var string
     */
    public $file_prefix;
    
    /**
     * Encription object.
     * It must have encrypt() and decrypt() methods that takes and returns a string.
     *
     * @var object
     */
    public $encrypt_object;

    /**
     * Constructor.
     *
     * @param string $save_path Session files directory. Use the PHP default path if null. Example: '/home/user/tmp'.
     * @param string $encrypt_object Optional encryption object with encrypt() and decrypt() methods.
     * @param string $file_prefix Session file name prefix.
     */
    public function __construct($save_path=null, $encrypt_object=null, $file_prefix='sess_') {
        parent::__construct();
        if ($save_path != null) {
            $this->save_path = realpath($save_path);
        } # Else, it will be set with the open() method
        $this->encrypt_object = $encrypt_object;
    }
    
    /**
     * Open a session.
     *
     * @param string $save_path Save path of the session.
     * @param string $session_name Session name.
     */
    public function open($save_path, $session_name) {
        if (!$this->save_path) {
            $this->save_path = $save_path;
        }
    }
    
    /**
     * Is executed when the session operation is done.
     */
    public function close() {
    }
    
    /**
     * Read session data from a file.
     *
     * @param string $session_id Sesion identifier.
     * @return string Session data. Return a void string if there is no data to read.
     */
    public function read($session_id) {
        if ($this->encrypt_object) {
            return $this->encrypt_object->decrypt((string) @file_get_contents($this->_build_file_path($session_id)));
        } else {
                return (string) @file_get_contents($this->_build_file_path($session_id));
            }
    }
    
    /**
     * Write the session data into a file.
     *
     * @param string $session_id Sesion identifier.
     * @param mixed $session_data Session data.
     */
    public function write($session_id, $session_data) {
        if ($fo = fopen($this->_build_file_path($session_id), 'w')) {
            if ($this->encrypt_object) {
                $return = fwrite($fo, $this->encrypt_object->encrypt($session_data));
            } else {
                $return = fwrite($fo, $session_data);
            }
            fclose($fo);
            return $return;
        } else {
            return false;
        }
    }
    
    /**
     * Destroy a session file.
     *
     * @param string $session_id Sesion identifier.
     */
    public function destroy($session_id) {
        return(@unlink($this->_build_file_path($session_id)));
    }
    
    /**
     * Remove the old session files.
     *
     * @param int $max_life_time Maximum life time of session data to remove.
     */
    public function gc($max_life_time) {
        foreach (glob($this->_build_file_path('*')) as $file_name) {
            if (filemtime($file_name) + $max_life_time < time()) {
                @unlink($file_name);
            }
        }
    }
    
    /**
     * Build the session file path.
     *
     * @param string $session_id Sesion identifier.
     */
    private function _build_file_path($session_id) {
        return $this->save_path . '/' . $this->file_prefix . (string) $session_id;
    }
}
?>