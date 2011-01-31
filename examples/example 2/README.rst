Informations about the example 2
================================

This example is the standard way for a quick web site with some static pages.

At the beginning of index.php there are the global variables that you can customize.
In the second part is loaded Phraw and the Smarty template engine with the Phraw extension.
In the third part are declared the pages to load. The keys are the urls and the values are the template pages to load. The url is matched in a regular expresion.
The fourth part match and load the static pages and a dynamic page with a dynamic url. The function static_route() is a shortcut.
