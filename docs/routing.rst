Routing
=======

Phraw give the foundamentals to build a flexible routing mechanism.
The goals are to simplify the creation of basic routing configurations for simple websites and both be very flexible for complex web applications.

The routing logic can be written in the ``index.php`` file or in a separate file or also splitted in many files, the choise is yours depending by the complexity of the application.

There is no need of mod_rewrite, but it is strongly suggested.

There are no limits on what a developer can do.

Routing basics
--------------

.. attention::

    | There are some changes since Phraw-0.4 in order to make things more explicit.    
    | The ``route()`` method have changed the syntax to handle regular expressions. Now the begin ``^`` and end ``$`` chars are not added by default for regular expressions.
    | The Phraw's ``$url`` attribute is renamed in ``$uri``, because it does not contains the full URL but just the URI.
    | The Phraw's ``$request`` attribute is renamed in ``$uri_values``, because it contains only the URI matched values and not a full request.
    | The leading slash ``/`` is removed from the Phraw's ``$uri`` attribute (the old ``$url`` one), because it is alwais present.

The main function is the ``route()`` method. It takes an URI string, verify if matches the request URI and store the possible values inside a Phraw's attribute.

The leading slash of the request URI is always removed because every URL has that.

Phraw supports three matching algorithms: reqular expressions (default), regular expressions on parentheses and equals matching.

The Phraw's $uri_values attribute will contains the values extracted from the matched URI.

The URI is catched automatically by Phraw in the $uri attribute when using mod_rewrite.
Optionally it is possible to use the Phraw's constructor for catch the URI from a GET parameter.
This is an example for the URL ``http://www.example.com/?u=/foo/bar/``:

.. code-block:: php

    <?php
    # ...
    $phraw = new Phraw('u');
    
    if ($phraw->route('foo/bar/', 'equal')) {
        # ...
    }
    # ...
    ?>

.. class:: Phraw

    .. attribute:: Phraw->uri
    
        URI automatically catched from the URL.
    
    .. attribute:: Phraw->uri_values
    
        The ``route()`` method will fill this attribute with the values extracted from the matched URI.
        
    .. method:: Phraw->constructor($uri_key=null)
    
        Sets the ``$uri`` attribute.
        
        ``$uri_key`` [null|string] if set will fill the ``$uri`` attribute with the GET parameter name given. Useful when mod_rewrite is not available.

    .. method:: Phraw->route($uri, $function='rexp')
    
        URI matching. The matching values are stored in $this->uri_values. The route mechanism can use a built-in function or a custom function passed by name.
        
        Returns ``true`` if the URI is matched.
        
        ``$uri`` [string] is the URI path you want to resolve.
        
        ``$function`` [string|array] is the matching method to use. The values can be: 'rexp' (default) for regular expressions, 'prexp' for regular expressions with parentheses and 'equal' for equal comparison.
        The value can also be a function name or an array for use class/objects methods (see `<http://www.php.net/manual/en/function.call-user-func-array.php>`_).

Regular expressions
^^^^^^^^^^^^^^^^^^^

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

The difference between simple regular expressions is that the regular expresions are evaluated only inside parentheses. This simplifies a lot the job in exchange of a little computational effort.

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

Custom route
^^^^^^^^^^^^

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

.. class:: Phraw

    .. method:: Phraw->detect_no_trailing_slash()
    
        Detects if there is the trailing slash in the URI.
    
    .. method:: Phraw->fix_trailing_slash()
    
        Fix the URI adding the trailing slash. It does a permanent redirect to the correct URL.

Routing shortcuts
-----------------

A chain of if-else statments could became very long and difficult to read.
Phraw offers some shortcuts for keep the job easy when there are similar URIs to match.

All methods leverages arrays because are easy to use in many manners, so are more flexible than routing classes/objects, are faster to process and can be serialized on a file (XML, JSON or whatever).

Bulk route for list of URIs
^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. attention::

    Since Phraw-0.4, the ``bulk_route()`` ``assign`` parameter accepts only a variable, no more a false value.
    The return value of the funtion will be only a boolean.

This is the simplest method to use when there are many similar pages to match.

``bulk_route()`` iterate an array to find the URI that mathes. An example:

.. code-block:: php

    <?php
    # ...
    
    $static_pages = array(
        '' => 'index.html',
        'about/' => 'about.html',
        'contacts/' => 'contacts.html',
        'documentation/' => 'documentation/index.html'
    );
    
    if ($phraw->detect_no_trailing_slash()) { # Fix the trailing slash
        $phraw->fix_trailing_slash();
    } else if ($phraw->bulk_route($static_pages, $page_found, 'equal')) { # Bulk routing
        $smarty->display($page_found);
    }
    # ...
    ?>

To use ``bulk_route()`` create an array of pages. Use the URIs as keys. The value can be what you want, in this case are the template file names.

The ``bulk_route`` takes the array, a variable to fill with the custom value of the matched page and the matching algorithm like the ``route()`` method.

It is possible to pass an array for values:

.. code-block:: php

    <?php
    # ...
    
    $static_pages = array(
        '' => array('index.html', 'section' => 'main'),
        'about/' => array('about.html', 'section' => 'main'),
        'contacts/' => array('contacts.html', 'section' => 'contact'),
        'documentation/' => array('documentation/index.html', 'section' => 'documentation')
    );
    
    if ($phraw->detect_no_trailing_slash()) { # Fix the trailing slash
        $phraw->fix_trailing_slash();
    } else if ($phraw->bulk_route($static_pages, $values, 'equal')) { # Bulk routing
        $smarty->assign('section' => $values['section']);
        $smarty->display($values[0]);
    }
    # ...
    ?>

It can be used an object attribute for the ``$assign`` parameter:

.. code-block:: php

    <?php
    # ...
    
    $static_pages = array(
        #...
    );
    
    class A {
        public $page_found;
    }
    
    $a = new A();
    
    if ($phraw->bulk_route($static_pages, $a->page_found)) {
        $smarty->display($a->page_found);
    }
    # ...
    ?>

...or also an array with default values:

.. code-block:: php

    <?php
    # ...
    
    $static_pages = array(
        #...
    );
    
    $a = array('foo' => 'bar');
    
    if ($phraw->bulk_route($static_pages, $a['page_found'])) {
        $smarty->assign('foo', $a['foo'])
        $smarty->display($a['page_found']);
    }
    # ...
    ?>

.. class:: Phraw

    .. method:: Phraw->bulk_route(&$uri_list, &$assign, $function)
    
        URI matching for an array of pages. The matching values are stored in $this->uri_values. The route mechanism can use a built-in function or a custom function passed by name.
        
        Returns ``true`` if one the URIs is matched.
    
        ``&$uri_list`` [array] key-value array of URIs. The key is the URI to match, the value will be 
        
        ``&$assign`` [variable] variable where store the custom values of the matched URI.
        
        ``$function`` [string|array] is the matching method to use. The values can be: 'rexp' (default) for regular expressions, 'prexp' for regular expressions with parentheses and 'equal' for equal comparison.
        The value can also be a function name or an array for use class/objects methods (see `<http://www.php.net/manual/en/function.call-user-func-array.php>`_).

Bulk tree for a tree of URIs
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The ``tree_route()`` method helps when there are nested URIs.

This method is a big advantage in large applications and it's very similar to classes/objects routing systems that some frameworks and libraries have.

A simple example:

.. code-block:: php

    <?php
    # ...
    
    $tree_pages = array(
        '' => array(null, 'index.html'),
        'about/' => array(null, 'about.html'),
        'contacts/' => array(null, 'contacts.html'),
        'documentation/' => array(
            array(
                '' => array(null, 'documentation/index.html'),
                'setup/' => array(null, 'setup.html'),
                'guide/' => array(null, 'guide.html'),
                'reference/' => array(null, 'reference.html'),
                'faq/' => array(null, 'faq.html'),
            )
        )
    );
    
    if ($phraw->tree_route($static_pages, $values, 'equal')) { # Tree routing
        $smarty->display($values[0]);
    }
    # ...
    ?>

It is possible to pass default values for the descendants.
In the next example the page "documentation/" -> "other/" have not a "page" value so it will be used a default page.

.. code-block:: php

    <?php
    # ...
    
    $tree_pages = array(
        '' => array(null, 'page' => 'index.html'),
        'about/' => array(null, 'page' => 'about.html'),
        'contacts/' => array(null, 'page' => 'contacts.html'),
        'documentation/' => array(
            array(
                '' => array(null, 'page' => 'documentation/index.html'),
                'setup/' => array(null, 'page' => 'setup.html'),
                'guide/' => array(null, 'page' => 'guide.html'),
                'reference/' => array(null, 'page' => 'reference.html'),
                'faq/' => array(null, 'page' => 'faq.html'),
                'other/' => array(null)
            ), 'page' => 'documentation/default.html'
        )
    );
    
    if ($phraw->tree_route($static_pages, $values, 'equal')) { # Tree routing
        $smarty->display($values['page']);
    }
    # ...
    ?>

Can be used more custom values. Custom values could be used for the view name, some parameters for the templates, some switches and so on.
In the example there is a "section" custom value for the templates:

.. code-block:: php

    <?php
    # ...

    $tree_pages = array(
        '' => array(null, 'page' => 'index.html', 'section' => 'main'),
        'about/' => array(null, 'page' => 'about.html', 'section' => 'main'),
        'contacts/' => array(null, 'page' => 'contacts.html', 'section' => 'contact'),
        'documentation/' => array(
            array(
                '' => array(null, 'page' => 'documentation/index.html'),
                'setup/' => array(null, 'page' => 'setup.html'),
                'guide/' => array(null, 'page' => 'guide.html'),
                'reference/' => array(null, 'page' => 'reference.html'),
                'faq/' => array(null, 'page' => 'faq.html'),
                'other/' => array(null)
            ), 'page' => 'documentation/default.html', 'section' => 'documentation'
        )
    );
    
    if ($phraw->tree_route($static_pages, $values, 'equal')) { # Tree routing
        $smarty->assign('section' => $values['section']);
        $smarty->display($values['page']);
    }
    # ...
    ?>

It can be used an object attribute for the ``$assign`` parameter:

.. code-block:: php

    <?php
    # ...
    
    $static_pages = array(
        #...
    );
    
    class A {
        public $page_found;
    }
    
    $a = new A;
    
    if ($phraw->tree_route($static_pages, $a->page_found)) {
        $smarty->display($a->page_found);
    }
    # ...
    ?>

...or also an array with default values:

.. code-block:: php

    <?php
    # ...
    
    $static_pages = array(
        #...
    );
    
    $a = array('foo' => 'bar');
    
    if ($phraw->tree_route($static_pages, $a['page_found'])) {
        $smarty->assign('foo', $a['foo'])
        $smarty->display($a['page_found']);
    }
    # ...
    ?>

.. class:: Phraw

    .. method:: Phraw->tree_route(&$uri_tree, &$assign, $function)
    
        URI matching for an tree of pages. The matching values are stored in $this->uri_values. The route mechanism can use a built-in function or a custom function passed by name.
        
        Returns ``true`` if one the URIs is matched.
    
        ``&$uri_list`` [array] key-value array of URIs. The key is the URI to match, the value will be 
        
        ``&$assign`` [variable] variable where store the custom values of the matched URI. The values, if in an array, are merged upside.
        
        ``$function`` [string|array] is the matching method to use. The values can be: 'rexp' (default) for regular expressions, 'prexp' for regular expressions with parentheses and 'equal' for equal comparison.
        The value can also be a function name or an array for use class/objects methods (see `<http://www.php.net/manual/en/function.call-user-func-array.php>`_).
