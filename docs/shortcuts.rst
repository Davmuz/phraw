Shortcuts
=========

Redirects
---------

HTTP redirection with the ``redirect()`` method.
It is possibile to display additional content in the redirect page because the method does not exit the script.

.. class:: Phraw

    .. method:: Phraw->redirect([string $url [, int $type = 301]])
    
        Sets the header with the given redirect type.
        
        ``$url`` URL to redirect. Use null for not add the "Location" header.
        
        ``$type`` Type of redirection.

Error and success headers
-------------------------

For convenience Phraw helps to set success and error headers.

.. class:: Phraw

    .. method:: Phraw->client_error([int $type = 404])
    
        Sets the header with the given error type.
        
        ``$type`` Type of client error.
    
    .. method:: Phraw->success_header([int $type = 200])
    
        Sets the header with the given success type.
        
        ``$type`` Type of success message.

Add an include path
-------------------

A simple method that prepend or append an include path.

.. code-block:: php

    <?php
    #...
    $phraw->add_include_path('lib'); # Add the "lib" directory to the include path
    # Load the Smarty extension from "lib/phraw/extensions/"
    require_once('phraw/extensions/smarty.php');
    $smarty = new SmartyTemplateEngine();
    ?>

.. class:: Phraw
    
    .. method:: Phraw->add_include_path(string $include_path, bool $append = false)
    
        Sets the header with the given redirect type.
        
        ``$include_path`` path to add to the include path.
        
        ``$append`` if true append the path, if false prepend it.

Get the request URI
-------------------

This method is used by the Phraw's constructor for fetch the request URI.

.. class:: Phraw
    
    .. method:: Phraw->get_uri([string $get_key = null])
    
        ``$get_key`` the name of the GET parameter that contains the URI.
        
        Returns the URI string.

Get the current domain
----------------------

Sometimes it is useful to get the current domain with the http/https protocol prefix in order to prepare absolute links in templates.

.. class:: Phraw
    
    .. method:: Phraw->get_current_domain()
    
        Returns protocol and domain name. Eg. http://www.mysite.com
