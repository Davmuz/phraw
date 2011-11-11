Scaling up
==========

With a full stack web framework, larger is the application and more are the problems with that framework.
This is because a big web application may need some features or behaviors that are not available on the framework, so often it is necessary to rewrite parts of the framework patching it.

Phraw is not a full stack framework, but a micro one.
This means that Phraw gives you only the bare bone things that you need and for the rest you can use your preferred libraries.

In reality, Phraw is quite different also from many of other micro-frameworks because it does not limit you on a particular workflow so you can chose the best method for you.

Structure
---------

Basic
^^^^^

A basic structure::

    lib/
    media/
    resources/
        cached/
        compiled/
        templates/
        views.php  --  view functions
        models.php  --  model layer
    index.php

Advanced
^^^^^^^^

For keep things in order, the code can be organized in modules.

An advanced structure::

    lib/
    static/
    media/
    resources/
        cached/
        compiled/
        templates/
        mymodule_1/
            views.php  --  view function of this module
        mymodule_2/
            templates/  --  templates of this module
            models.php  --  model layer of this module
            views.php  --  view function of this module
            *.php  --  other PHP files
        config.php  --  configuration bluepring
        config_local.php  --  personal configuration
        models.php  --  model initialization
    index.php

Some template engines, like Smarty, permit to use more template directories so each module can have one.

Someone prefer to organize things not in modules, but by layers::

    lib/
    static/
    media/
    resources/
        cached/
        compiled/
        templates/
        configs/
            config.php
            config_development.php
            config_production.php
            config_staging.php
        models/
            base.php
            models_one.php
            models_two.php
            models_three.php
        views/
            views_one.php
            views_two.php
            views_three.php
    index.php

Configuration
-------------

Internal configuration
^^^^^^^^^^^^^^^^^^^^^^

A classic configuration may looks like this:

.. code-block:: php

    <?php
    # Config
    define('DEBUG', true); # Development mode
    define('RESOURCES_DIR', true);
    $config = array(
        # ... Your configuration parameters
    );
    
    # Load Phraw
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # ...
    ?>

External configuration
^^^^^^^^^^^^^^^^^^^^^^

An external configuration may be easier to maintain.

On "index.php":

.. code-block:: php

    <?php
    # Config
    define('RESOURCES_DIR', 'resources');
    require_once(RESOURCES_DIR . '/config.php');
    
    # Load Phraw
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # ...
    ?>

On "resources/config.php":

.. code-block:: php

    <?php
    define('DEBUG', true); # Development mode
    $config = array(
        # ... Your configuration parameters
    );
    ?>

Local configuration
^^^^^^^^^^^^^^^^^^^

The file "resources/config.php" have the default values, on the contrary the "resources/config_local.php" file have the current values.

To use the local configuration modify the "index.php" file:

.. code-block:: php

    <?php
    if (@include_once('resources/config_local.php')) {
        # ... Local configuration is loaded
    } else {
        # ... Local configuration is not loaded
    }
    
    # Load Phraw
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # ...
    ?>

Routing
-------

There are special things that can be done when routing is so flexible.

Selective middleware
^^^^^^^^^^^^^^^^^^^^

The middleware is a function or an object that is loaded before and after the view.

Load the middleware only when requested, this is just a bare bone example:

.. code-block:: php

    <?php
    # ...

    $static_pages = array(
        '' => array('index.html', 'load_middleware' => false),
        'about/' => array('about.html', 'load_middleware' =>  true),
        'contacts/' => array('contacts.html', 'load_middleware' =>  true),
        'documentation/' => array('documentation/index.html', 'load_middleware' =>  false),
    );
    
    # Routing
    if ($phraw->detect_no_trailing_slash()) {
        $phraw->fix_trailing_slash();
    } else if ($phraw->bulk_route($static_pages, $parameters, 'equal')) {
        require_once(RESOURCES_DIR . '/views.php');
        if ($parameters['load_middleware']) { # Execute the middleware
            $mymiddleware = new MyMiddleware();
            $mymiddleware->before();
            view_standard($phraw, $smarty, $parameters);
            $mymiddleware->after();
        } else { # Do not execute middleware
            view_standard($phraw, $smarty, $parameters);
        }
    } else {
        $smarty->display_error();
    }

Advanced error handling
^^^^^^^^^^^^^^^^^^^^^^^

Something goes wrong on views? It is possible to handle and manage errors easily writing logs, sending email reports and so on.

There are many third-party libraries for error handling, why not use those?

An example:

.. code-block:: php

    <?php
    # ...
    
    $static_pages = array(
        '' => 'index.html',
        'about/' => 'about.html',
        'contacts/' => 'contacts.html',
        'documentation/' => 'documentation/index.html'
    );
    
    try {
        if ($phraw->detect_no_trailing_slash()) { # Fix the trailing slash
            $phraw->fix_trailing_slash();
        } else if ($phraw->bulk_route($static_pages, $page_found, 'equal')) { # Bulk routing
            $smarty->display($page_found);
        } else {
            $smarty->display_error();
        }
    catch (Exception $error) {
        # ... Manage here the errors like send an email report to the develop team
    }
    ?>

ORMs
----

Many frameworks includes an ORM system but often it is not mature enough or for particular projects it is limited; changing it may be a pain.

Phraw is ORM agnostic: it is possible to chose the preferred ORM from the start or use PDO directly.

Phraw
-----

It is possible to replace things of Phraw simply subclassing it. Phraw is so thin and simple that this is an easy job.

Can be created more advanced routing helpers (like ``bulk_route()`` or ``tree_route()``) in few minutes.
The ``route()`` method can runs custom matching algorithms or be replaced entirely without problems.

The session framework can be easily extended subclassing SessionSaveHandler or replaced with something else, it's just a function!

Phraw is template agnostic: you can use your preferred template engine, more than one at the same time or no one.
