Informations about the example 1
================================

This example is an easy start for a quick web site with some static pages and a dynamic page.

At the beginning of index.php there are the global and configurations variables that you can customize.

In the second part is loaded Phraw and added the "lib" directory to the include path.

In the third part is loaded Smarty through the Phraw extension that prepare Smarty for us. It is still possible to use Smarty without the extension.

In the fourth part are prepared some static pages to load. The keys are the URLs to match and the values are the template pages to load. The URL is matched in a regular expresion. The values can also be arrays, objects or other things.

The fifth part is the routing section built by IF-ELSE blocks, each match loads a different content. Each block can be used as a small, very fast, view function.
The first IF is a plain example.
The second IF detect if there is not a trailing slash and add it if not found.
The third IF display one of the static pages if one is detected.
The fourth IF get a parameter from the URL and display a dynamic page from a module.
If there are not pages matched display a default 404 error page (Page Not Found).
