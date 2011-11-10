Setup
=====

Follow the :doc:`installation` guide for a quick setup.

Directory structure
-------------------

Basically a web project requires a repository where store the third party libraries, a place where put public files (like photos, JavaScripts, CSSs and so on) and the PHP files of the project.
This documentation only suggest a directory structure generally good.

This is the suggested directory structure::

    lib/
    static/
    media/
    resources/
        cached/
        compiled/
        templates/

The ``lib`` directory contains the libraries, like Phraw.

The ``static`` directory contains the files that compose the HTML front end like images, CSS, JavaScript and so on.

The ``media`` directory will contains the user files like images, video and documents.

The ``resources`` directory contains the files that compose the web site. Inside there are: ``cached`` for automatic cached templates, ``compiled`` for automatic compiled templates, and ``templates`` for source templates.
The project modules (vews and models) should live inside the ``resources`` directory because it should be protected by reading.

Error reporting
---------------

Importing the main Phraw file will setup automatically the error reporting.
It should be the first thing to do because errors should be not diplayed in the browser if the application is on production and on the contrary if it is on development.

.. code-block:: php

    <?php
    # The first thing to do is this:
    require_once('lib/phraw/phraw.php');
    
    # ... the rest of the application
    ?>

The error reporting configuration is very simple so it can be personalized, it only displays or hide errors on the browser.

The global constant ``DEBUG`` defines if display errors or not. Some extensions uses this constant for their specific setups. By default it is set to ``false``.

Load Phraw
----------

Load Phraw is very simple:

.. code-block:: php

    <?php
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # ...
    ?>

Phraw automatically loads the URI from the HTTP request, see the :doc:`routing` guide for more informations.
It is possible to load the URI from a GET parameter simply giving its name:

.. code-block:: php

    <?php
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw('u'); # Load the 'u' GET parameter
    
    # ...
    ?>

.. class:: Phraw

    .. method:: Phraw->__constructor([string $uri_get_key=null])
    
        ``$uri_get_key`` the name of the GET parameter that contains the URI.

Configuration
-------------

These are the global constants used by Phraw:

DEBUG (default ``false``)
    Activate the debug mode if ``true`` showing errors on browser.
    
    This configuration constant is the only required before including the "phraw.php" file.

RESOURCES_DIR (default "resources")
    Resource directory.
