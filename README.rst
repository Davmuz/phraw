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

Understand the GIT workflow
---------------------------

Branches:

- **Master:** stable releases. Here are only the official stable releases.
- **Develop:** main development branch. Use this if you want to test the new features or for contribute.

**Release** candidate versions are tagged ``release-X`` where ``X`` will be the final version. Try release versions for bleeding edge code or to help to debug the code before the official release.
