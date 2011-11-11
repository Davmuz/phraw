MVC and similar patterns
========================

Phraw can be used with a MVC pattern, a MVP pattern and similars because does not obligate the use on one or another: Phraw does not make a choice for you.

The MVC pattern is very popular for web applications. MVC stands for Model, View, Controller. This little guide show how to simply use it.

The Controller is Phraw itself and the router section: takes the requests from the browser and coordinate the application.

The Model is the layer that operate on datas like databases, files and so on.

The View is the layer that creates the response for the browser.

There is another layer, the Presenter, that are the templates; this is because a template engine, like Smarty, can execute a bit of logic in order to generate the HTML.

MVCP example
------------

In this example we build a tiny website that shows a name selected from a little database.

Controller
^^^^^^^^^^

For more informations see the :doc:`routing` guide.

In the "index.php" file, simply create the routing rules, this is just a simple example:

.. code-block:: php

    <?php
    define('DEBUG', true); # Development mode
    
    # Load Phraw with the starter shortcut
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    # Load the Smarty extension
    require_once('phraw/extensions/smarty.php');
    $smarty = new SmartyTemplateEngine();
    
    if ($phraw->detect_no_trailing_slash()) { # Fix the trailing slash
        $phraw->fix_trailing_slash();
    } else if ($phraw->route('', 'equal')) { # Routing for the home page
        require_once('resources/views.php');
        view_home($phraw, $smarty);
    } else { # Page not found
        $smarty->display_error();
    }
    ?>

Models
^^^^^^

Create the "resources/models.php" file. Here is where put database functions and objects. The file name "models.php" is just a suggestion.

In this example build an array to use like a database:

.. code-block:: php

    <?php
    $people = array('Mario', 'Larry', 'Homer', 'Miriam');
    ?>

View
^^^^

Create the "resources/views.php" file. Here is where put view functions or objects. The file name "views.php" is just a suggestion.

The view displays the name requested by the ``$_GET['name']`` parameter.

.. code-block:: php

    <?php
    require_once('resources/models.php'); # Load the models
    
    function view_home(&$phraw, &$smarty) {
        global $people;
        
        if (!isset($_GET['name'])) {
            $smarty->assign('name', null);
        } else if (isset($people[(int) $_GET['name']])) {
            $smarty->assign('name', $people[(int) $_GET['name']]);
        } else {
            $smarty->assign('name', false);
        }
        
        $smarty->assign('people', $people);
        $smarty->display('home.html');
    }
    ?>

Presenter
^^^^^^^^^

Create the template "resources/templates/home.html".

.. code-block:: html

    <html>
        <body>
            {if $name === false}
            <p>Sorry, the name is not on the database.</p>
            {else if $name === null}
            <p>No name selected.</p>
            {else}
            <p>The name is {$name}</p>
            {/if}
            <p>Please, chose a name:</p>
            <ol>
                {foreach $people as $key => $value}<li><a href="/?name={$key}">{$value}</a></li>{/foreach}
                <li><a href="/?name=70">Fake</a></li>
            </ol>
        </body>
    </html>

Now try to see this little web site on the browser.
