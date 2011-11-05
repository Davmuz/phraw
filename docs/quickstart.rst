Quick start
===========

This is a quick tutorial for creating a basic web site. Follow the :doc:`installation <installation>` instructions if you haven't.

A basic "hello world" example
-----------------------------

The web site logic is written in a ``index.php`` file. Create it and write the following:

.. code-block:: php

    <?php
    # Global variables: place here your global settings
    define('DEBUG', true); # Activate the debug mode
    
    # Load Phraw with the starter shortcut
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # Routing
    if ($phraw->route('^$')) { # root URL
        echo 'Hello world';
    }
    ?>

The first block is where set the configuration variables, actually we do it in the simplest way. The ``DEBUG`` constant will be read by Phraw and is used to activate the PHP debug mode.

The second block loads the framework and instantiate a Phraw object. Automatically Phraw reads the request URI and stores it; this behavior can be changed if needed.

The third block is the routing logic. This is not the only way to handle routing but it is the faster and the most flexible one.
The ``route`` method returns true if the given pattern matches the URI. The default algorithm used is a standard regular expression, but can be changed, so the string '^$' refers to the root URI '' or '/'.

Ok, now it is possible to run the browser and see the "hello world" page!

Adding a template
-----------------

For a template we need a template engine. You can use the template engine that you prefer, but in this tutorial we will use Smarty. Refer to the `official Smarty documentation <http://www.smarty.net/>` for more informations.

Go in the ``resources/templates/`` directory and create a ``base.html`` file:

.. code-block:: html

    <html>
    <head>
        <title>Base title</title>
    </head>
    <body>
        <p>Hello world!</p>
    </body>
    </html>

We now have to load the template engine, Phraw comes with an extension for Smarty:

.. code-block:: php

    <?php
    # ...
    require_once('lib/phraw/extensions/smarty.php');
    $smarty = new SmartyTemplateEngine();
    # ...
    ?>

And now the routing logic:

.. code-block:: php

    <?php
    # ...
    # Routing
    if ($phraw->route('^$')) { # root URL
        $smarty->display('base.html');
    }
    ?>

Run the browser and see the page loaded from the template.

This is the full code:

.. code-block:: php

    <?php
    # Global variables: place here your global settings
    define('DEBUG', true); # Activate the debug mode
    
    # Load Phraw with the starter shortcut
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # Load the Smarty extension
    require_once('lib/phraw/extensions/smarty.php');
    $smarty = new SmartyTemplateEngine();
    
    # Routing
    if ($phraw->route('^$')) { # root URL
        $smarty->display('base.html');
    }
    ?>

The 404 error page
------------------

If we point the browser to a wrong URI, like ``http://example.com/foobar/`` we get a blank page, so improve the behavior adding a 404 error message:

.. code-block:: php

    <?php
    # ...
    # Routing
    if ($phraw->route('^$')) { # root URL
        $smarty->display('base.html');
    } else { # Page not found
        $phraw->client_error(404);
        echo 'Page not found';
    }
    ?>

If the URI is not matched sets the 404 error header and prints the 'Page not found' message.

Try browsing a wrong page.

The lone message "Page not found" is ugly, adding a custom 404 error page with a template is very simple.

Create the ``resources/templates/404.html`` file like:

.. code-block:: html

    <html>
    <head>
        <title>Error 404</title>
    </head>
    <body>
        <h1>Error 404</h1>
        <p>Page not found!</p>
    </body>
    </html>

Then modify the ``index.php`` file:

.. code-block:: php

    <?php
    # ...
    # Routing
    if ($phraw->route('^$')) { # root URL
        $smarty->display('base.html');
    } else { # Page not found
        $smarty->display_error(404);
    }
    ?>

The ``display_error()`` method of the Smarty extension it's a shortcut that automatically adds the 404 error header and prints the 404.html template in one step. Passing the "404" integer parameter is optional because it is the default value.

This is the full code:

.. code-block:: php

    <?php
    # Global variables: place here your global settings
    define('DEBUG', true); # Activate the debug mode
    
    # Load Phraw with the starter shortcut
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # Load the Smarty extension
    require_once('lib/phraw/extensions/smarty.php');
    $smarty = new SmartyTemplateEngine();
    
    # Routing
    if ($phraw->route('^$')) { # root URL
        $smarty->display('base.html');
    } else { # Page not found
        $smarty->display_error(404);
    }
    ?>

Leverage the base.html file
---------------------------

All the pages of the web site should have the same structure, at least for the header and the footer. So we will declare some blocks in the ``base.html`` file that will be replaced by the other pages of the web site. This will result in much less code to write and a flexible way to do the things.

Here the new ``resources/templates/base.html`` file with the named blocks:

.. code-block:: smarty

    <html>
    <head>
        <title>{block name='title'}Base title{/block}</title>
    </head>
    <body>
        <p>[header]</p>
        {block name='content'}Hello world!{/block}
        <p>[footer]</p>
    </body>
    </html>

The modified resources/templates/404.html file is:

.. code-block:: smarty

    {extends file='base.html'}
    
    {block name='title'}Error 404{/block}</title>
    
    {block name='content'}
    <h1>Error 404</h1>
    <p>Page not found!</p>
    {/block}

Yes, that's all the code and without using PHP, this is Smarty!

The first line loads the ``base.html`` file and use it as a framework.

The second line replaces the "title" block.

The third line replaces the "content" block.

Now try it on the browser: the 404 error page is engaged on the base page.

Dynamic pages
-------------

Now we add a new page with a dynamic variable. (Phraw have also a shortcut that leverages an array in order to display many static pages in just one step, but it's not important now)

Create the routing logic for the URL ``http://example.com/hello/``:

.. code-block:: php

    <?php
    # ...
    # Routing
    if ($phraw->route('^$')) { # root URL
        $smarty->display('base.html');
    } else if ($phraw->route('^hello\/?$')) { # Say-hello page
        $smarty->assign('name', 'Mario');
        $smarty->display('say-hello.html');
    } else {
        $smarty->display_error();
    }
    ?>

The routing string '^hello\/?$' is still a regular expression that matches the URL 'http://example.com/hello' or 'http://example.com/hello/'.
The Smarty ``assign()`` method simply assign the variable name 'name' the value 'Mario'.

Create a new page resources/templates/say-hello.php:

.. code-block:: smarty

    {extends file='base.html'}
    
    {block name='title'}Say hello{/block}</title>
    
    {block name='content'}
    <p>Hello {$name}</p>
    {/block}

The variable 'name' will be printed over the placeholder '{$name}'.

Browse the page an see the site that says hello to Mario.

The variable 'name' is hardcoded. It is possible to get it from the `GET` global variable using the URL ``http://example.com/hello\/?name=Mario``:

.. code-block:: php

    <?php
    # ...
    } else if ($phraw->route('^hello\/?$')) { # Say-hello page
        $smarty->assign('name', $_GET['name']);
        $smarty->display('say-hello.html');
    # ...
    ?>

Get values from the URL
-----------------------

It is also possible to obtain values directly from the URL ``http://example.com/hello/Mario/`` (great for SEO!):

.. code-block:: php

    <?php
    # ...
    } else if ($phraw->route('^hello\/(?P<name>\.*)\/?$')) { # Say-hello page
        $smarty->assign('name', $phraw->uri_values['name']);
        $smarty->display('say-hello.html');
    # ...
    ?>

Phraw automatically extracts the patterns from the regular expression and stores it into the ``requests`` property.

The regular expression looks ugly? No problem, there are other optional algorithms that you can use: see the API reference guide for more informations.

Conclusions
-----------

There are more more features ready to use. Phraw is very flexible and highily customizable, continue to read this documentation and discover how it can help you with your projects.
