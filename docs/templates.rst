Templates
=========

Phraw is template agnostic: you can use your preferred template engine, more than one at the same time or no one.

Phraw comes with some extensions for major template engines. Actually are supported Smarty and RainTPL.

The extensions are optional but helps to bootstrap the chosen template engine in only one line of code.

Smarty
------

See the `official web site <http://www.smarty.net/>`_.

This is the suggested template engine.

Smarty is powerful, extensible, fast enough and well supported.

The extension requires the global constant ``RESOURCES_DIR`` to be set with the path to the resources directory.

Initialization:

.. code-block:: php

    <?php
    # ...
    # Load the Smarty extension
    require_once('lib/phraw/extensions/smarty.php');
    $smarty = new SmartyTemplateEngine();
    
    # Display a page
    $smarty->display('base.html');
    # ...
    ?>

.. class:: SmartyTemplateEngine

    .. method:: SmartyTemplateEngine->__construct([int $caching = 1])
    
        Configure Smarty. Requires the global constant ``RESOURCES_DIR`` to be set.
        
        ``$caching`` see the caching feature on the Smarty official documentation.
        Enabled by default and disable when the global constant ``DEBUG`` is seto to ``true``.
    
    .. method:: SmartyTemplateEngine->display_error([int $type = 404])
    
        Display an error page. The template used will be the code plus the template extension.
        For example, a 404 error page will be the "404.html" template file.
        
        ``$type`` error type.

RainTPL
-------

See the `official web site <http://http://www.raintpl.com//>`_.

RainTPL is very fast and very easy to use because has only simple features. It is suggested for simple templates that requires a high throughput.

The extension requires the global constant ``RESOURCES_DIR`` to be set with the path to the resources directory.

Initialization:

.. code-block:: php

    <?php
    # ...
    # Load the RainTPL extension
    require_once('lib/phraw/extensions/raintpl.php');
    $raintpl = new RaintplTemplateEngine();
    
    # Display a page
    $raintpl->draw('base');
    # ...
    ?>

.. class:: RaintplTemplateEngine

    .. method:: RaintplTemplateEngine->__construct([int $caching = 1])
    
        Configure RainTPL. Requires the global constant ``RESOURCES_DIR`` to be set.
        
        ``$caching`` see the caching feature on the Smarty official documentation.
        Enabled by default and disable when the global constant ``DEBUG`` is seto to ``true``.
    
    .. method:: RaintplTemplateEngine->display_error([int $type = 404])
    
        Display an error page. The template used will be the code plus the template extension.
        For example, a 404 error page will be the "404.html" template file.
        
        ``$type`` error type.

Create an extension to support a different template engine
----------------------------------------------------------

The extension should configure the template engine with basic parameters (template directory and caching) and provide a ``display_error(int $type = 404)`` method.

See the Smarty extension for a concrete example.
