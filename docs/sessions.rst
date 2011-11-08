Sessions
========

Phraw does not limit the developer for their session system, but offers a simple session framework for create new custom session handlers.
Loading and starting a session is just one line.

The method ``session_start()`` takes a class string, that handle the sessions, and some parameters.
The session class is loaded automatically with the given parameters and starts immediately.

The session handler object will be stored in the ``$session_handler`` property inside the Phraw object.

The following example loads and starts a ``SessionFilesHandler`` class with the parameter "/home/user/tmp" (stores the sessions in that directory), then sets and uses a session variable:

.. code-block:: php

    <?php
    # ...
    require_once('lib/phraw/extensions/sessions_files.php'); # Include the file that contains the SessionFilesHandler class
    $phraw->session_start('SessionFilesHandler', '/home/user/tmp'); # Load and start the session
    
    # Set a session variable
    $_SESSION['foo'] = 'bar';
    
    # ...
    
    # Use the session variable
    echo $_SESSION['foo'];
    ?>

.. class:: Phraw

    .. attribute:: Phraw->session_handler
    
        Will have the session handler object.
    
    .. method:: Phraw->session_start(callback $class [, mixed $...])
    
        ``$class`` SessionSaveHandler extended class name.
        
        The additional parameters will be passed to the session class constructor.

Built-in session handlers
-------------------------

Phraw have a session handler ready to use.

Session Files Handler
^^^^^^^^^^^^^^^^^^^^^

Increase the security respect than the standard session handler of PHP.

The sessions can be stored in a custom directory.

It is possible to encrypt the session files giving an object with ``encrypt()`` and ``decrypt()`` methods.

:module: extensions/sessions_files.php

.. class:: SessionFilesHandler
    
    .. attribute:: SessionFilesHandler->encrypt_object
    
        Encription object.
    
    .. attribute:: SessionFilesHandler->file_prefix
    
        Session file name prefix.
    
    .. attribute:: SessionFilesHandler->save_path
    
        Session files directory path. The given directory must be writable by PHP.
    
    .. method:: SessionFilesHandler->__construct([string $save_path = null [, string $encrypt_object = null [, string $file_prefix = 'sess_' ]]])

        ``$save_path`` session files directory. Use the PHP default path if null. Example: '/home/user/tmp'.
        
        ``$encrypt_object`` optional encryption object with encrypt() and decrypt() methods.
        
        ``$file_prefix`` adds a prefix to session file names in order to prevent clashes.

Custom session handlers
-----------------------

Making a new session handler is easy: simply create a new class extending ``SessionSaveHandler`` with a concrete implementation of session functions.

With a very little effort it is possible to keep sessions on a database, on a NO-SQL database, in RAM like Memcached or whatever else.

`See the PHP manual for more informations. <http://www.php.net/manual/en/function.session-set-save-handler.php>`_

:module: extensions/sessions.php

.. class:: SessionSaveHandler
    
    .. method:: SessionSaveHandler->open($save_path, $session_name)
    
        `See the PHP manual. <http://www.php.net/manual/en/function.session-set-save-handler.php>`_
    
    .. method:: SessionSaveHandler->close()
    
        `See the PHP manual. <http://www.php.net/manual/en/function.session-set-save-handler.php>`_
    
    .. method:: SessionSaveHandler->read($session_id)
    
        `See the PHP manual. <http://www.php.net/manual/en/function.session-set-save-handler.php>`_
    
    .. method:: SessionSaveHandler->write($session_id, $session_data)
    
        `See the PHP manual. <http://www.php.net/manual/en/function.session-set-save-handler.php>`_
    
    .. method:: SessionSaveHandler->destroy($session_id)
    
        `See the PHP manual. <http://www.php.net/manual/en/function.session-set-save-handler.php>`_
    
    .. method:: SessionSaveHandler->gc($max_life_time)
    
        `See the PHP manual. <http://www.php.net/manual/en/function.session-set-save-handler.php>`_
