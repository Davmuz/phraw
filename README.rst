Phraw
=====

Phraw is a PHP micro-framework, his scope is to help build small web sites and web applications. Phraw is very fast and easy to learn because it is thin.

The framework is very flexible and does not require a database (but you can also use it if you want). The default template engine is Smarty (still optional), and you can change it with your preferred one.

Details and example applications are a vailable on `the Phraw official website <http://phraw.dav-muz.net/>`_.

Just a small example
--------------------

    <?php
    require_once('lib/phraw/phraw.php');
    $phraw = new Phraw();
    
    if ($phraw->route('')) {
        echo 'Hello world!';
    }
    ?>
