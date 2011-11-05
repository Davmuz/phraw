Routing
=======

Phraw give the foundamentals to build a flexible routing mechanism.
The goals are to simplify the creation of basic routing configurations for simple websites and both be very flexible for complex web applications.

The routing logic can be written in the ``index.php`` file or in a separate file or also splitted in many files, the choise is yours depending by the complexity of the application.

There are no limits on what a developer can do.

Routing basics
--------------

The main function is the ``route()`` method. It takes an URI string, verify if matches the request URI and store the possible values inside a Phraw's attribute.

The leading slash of the request URI is always removed because every URL has that.

Phraw supports three matching algorithms: reqular expressions (default), regular expressions on parentheses and equals matching.

Regular expression
^^^^^^^^^^^^^^^^^^

Regular expressions are the most powerful method to match an URI and extract values from it, at the little cost of human readability.

The example matches ``/foo/bar/`` but also ``/foo/bar``:

.. code-block:: php

    <?php
    # ...
    if ($phraw->route('^foo\/bar\/?$', 'rexp')) {
        # ...
    }
    # ...
    ?>

It is possible to extract values from the URI. This example will print the 'name' named value.

.. code-block:: php

    <?php
    # ...
    if ($phraw->route('^foo\/(?P<name>\w+)\/?$', 'rexp')) {
        echo 'The variable is: ' . $phraw->uri_values['name'];
    }
    # ...
    ?>

Regular expressions on parentheses
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The difference between simple reqular expressions is that the regular expresion are evaluated only inside parentheses. This simplifies a lot the job in exchange of a little computational effort.

The example matches only ``/foo/bar/``:

.. code-block:: php

    <?php
    # ...
    if ($phraw->route('^foo/bar/$', 'prexp')) {
        # ...
    }
    # ...
    ?>

In order to match also ``/foo/bar/`` there is a bit of code to add:

.. code-block:: php

    <?php
    # ...
    if ($phraw->route('^foo/bar(\/?)$', 'prexp')) {
        # ...
    }
    # ...
    ?>

...because the regular expression is evaluated only in the parentheses. There is a better way to do this using the method ``fix_trailing_slash()``, explained in a following chapter.

It is possible to extract values from the URI. This example will print the 'name' named value.

.. code-block:: php

    <?php
    # ...
    if ($phraw->route('^foo/(?P<name>\w+)/$', 'prexp')) {
        echo 'The variable is: ' . $phraw->uri_values['name'];
    }
    # ...
    ?>

Equals match
^^^^^^^^^^^^

This is the simplest way to match an URI but can't extract values from it. It is very handy for static pages.

The example matches only ``/foo/bar/``:

.. code-block:: php

    <?php
    # ...
    if ($phraw->route('foo/bar/', 'equal')) {
        # ...
    }
    # ...
    ?>

Fix the trailing slash
----------------------

This feature is very important for SEO. The search engines may think that there is a duplication of content if the same page can reached both by ``http://www.yoursite.com/page/`` and ``http://www.yoursite.com/page``, this may penalize that page.

There is not a magic automatic mechanism like other frameworks because in certain cases it have to be possibile to implement a special behavior, so this feature is implemented in two simple methods that can be used separately or replaced with custom functions.

The method ``detect_no_trailing_slash()`` detect when there is not the trailing slash at the end of the URLs, then the method ``fix_trailing_slash()`` can be used to redirect the user to the correct page.

Add the detection to the routing and use the fixer function:

.. code-block:: php

    <?php
    # ...
    
    if ($phraw->detect_no_trailing_slash()) { # Detect the absence of the trailing slash in the URI
        $phraw->fix_trailing_slash(); # Redirect the user to the correct URL
    }
    
    # ...
    ?>

Custom route
------------

It is possible to create a custom route algorithm using a simple function, an object method or a static method.

This feature can be used for custom algorithms or more complex behaviors like lookup pages on a database, CMSs, object-driven frameworks and so on.

The custom function or method have to take two variable references: the URI to match and the array variable where store the matched values.
The return value have to be a boolean: ``true`` if the URI is matched or ``false`` if not.

The values stored in the ``$uri_values`` array have to be by name, if named, an also by integer (the first value have the index 1, the second 2 and so on). The 0 value have to be the text that matched the full pattern.

A custom function:

.. code-block:: php

    <?php
    # ...
    
    function myroute(&$uri, &$uri_values) {
        # ...
    }
    
    if ($phraw->route('foo/bar/', 'myroute')) {
        # ...
    }
    # ...
    ?>

A custom class method:

.. code-block:: php

    <?php
    # ...
    
    class Routing {
        function myroute(&$uri, &$uri_values) {
            # ...
        }
    }
    
    if ($phraw->route('foo/bar/', array('Routing', 'myroute'))) {
        # ...
    }
    # ...
    ?>

A custom object method:

.. code-block:: php

    <?php
    # ...
    
    class Routing {
        function myroute(&$uri, &$uri_values) {
            # ...
        }
    }
    
    $routing = new Routing;
    
    if ($phraw->route('foo/bar/', array('routing', 'myroute'))) {
        # ...
    }
    # ...
    ?>
