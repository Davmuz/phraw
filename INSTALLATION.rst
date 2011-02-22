Phraw - installation
====================

Requirements
------------

PHP 5 (tested with PHP 5.3)
Smarty (optional but it's the default template engine)

Quick installation
------------------

1. Create your project directory (e.g. "mysite.com") and enter in it.
2. Create the "lib" directory and copy all your libraries, like Phraw and Smarty, in it.
3. Chose an example from the "examples" directory and use it as a template, remember to copy also the hidden file ".htaccess".
4. Set the directories "resources/compliled" and "resources/cached" writable by PHP.

Step-to-step installation
-------------------------

1. Create your project directory (e.g. "mysite.com") and enter in it.
2. Create a "lib" directory.
3. Copy the "phraw" directory in the "lib" one.
3. Install Smarty and other libraries in the "lib" directory.
4. Copy one of the examples in your project directory, remember to copy also the hidden file ".htaccess".
5. Set the directories "resources/compliled" and "resources/cached" as writables by PHP.
6. Read the "README.rst" file for more info.
7. Try to run it, it will display a wellcome page.
8. Customize the code.
9. Your site is now ready!

The final directory structure will be like:

::

    mysite.com
        lib
            ... (Smarty, Phraw and other libraries)
        resources
            cached
            compiled
            templates
            ... (your modules)
        media
            ... (your public files like images, CSS, JS ...)

Got a problem?
--------------

Set the constant "DEBUG" to true in order to see the errors.

These are the common errors:

- Have you copied the .htaccess file in the project directory?
- Your web server (like Apache) does support mod_rewrite? Is it active?
- The Smarty's compiled files are stored in the "resources/compliled" directory, is it writable?
- The Smarty's caching files are stored in the "resources/cached" directory, is it writable?
- You don't see the changes to the templates? Simply clear the cache and/or the compiled directories.
