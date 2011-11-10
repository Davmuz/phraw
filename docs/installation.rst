Installation
============

Phraw depends on external libraries but no one is required. It is suggested to download and install `Smarty <http://www.smarty.net/>`_ as template engine because it is fast, powerful and easy to use.

Quick installation
------------------

The faster way to install Phraw is to `download <http://phraw.dav-muz.net/downloads/>`_ a pre-packaged distribution. You should start with the vanilla distribution that contains Phraw and Smarty.

Follow the instructions:

#. Download the pre-packaged distribution.
#. Extract the files.
#. Copy all the files in the web directory on the web server. Pay attention to copy also the hidden .htaccess files.
#. Set the permissions as 777 o the directories: ``resources/cached/`` and ``resources/compiled/``.

The debugging is set to True by default in order to help you during the installation, you can change it in the index.php file.

Now visit the web site and you will see the working page.

Using Apache
^^^^^^^^^^^^

The pre-packaged distribution contains an ``.htaccess`` pre-build file that uses mod_rewrite.

mod_rewrite is optional but strongly suggested.

Using Nginx
^^^^^^^^^^^

Edit your site configuration file and add the following lines to your "location / {...}" section::

    location / {
        # ...
        
        if (!-f $request_filename) {
            rewrite ^.*$ /index.php last;
            break;
        }
        if (!-d $request_filename) {
            rewrite ^.*$ /index.php last;
            break;
        }
        
        # ...
    }

The rewrite feature is optional but strongly suggested.


Manual installation
-------------------

Here it is explained the reccommended way to do the manual installation, but Phraw is so flexible that you can change this method in one that you prefer.

The steps are:

# Creation of the directory structure
# Installation of the libraries (Phraw included)
# Creation of the web site starting from a brand new index.php file

Creation of the directory structure
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Open the web site directory an create this suggested structure::

    lib/
    static/
    media/
    resources/
        cached/
        compiled/
        templates/

The ``lib`` directory contains the libraries, like Phraw.

The ``static`` directory contains the files that compose the HTML front end like images, CSS, JavaScript and so on.

The ``media`` directory will contains the user files.

The ``resources`` directory contains the files that compose the web site. Inside there are: ``cached`` for automatic cached templates, ``compiled`` for automatic compiled templates, and ``templates`` for source templates.

In order to increase the security it is recommended to create and put the following ``.htaccess`` file in ``lib`` and ``resources`` directories::

    <Files *>
        Order deny,allow
        Deny from all
    </Files>


Installation of the libraries
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Smarty is not necessary, but it is used in this documentation so it is suggested to install it.

Download `Phraw <http://phraw.dav-muz.net/downloads/>`_ and `Smarty <http://www.smarty.net/>`_. Extract the two packages in the ``lib`` directory.

For a cleaner setup it is suggested to copy only the ``libs`` directory in the Smarty package and rename it ``smarty``. For Phraw copy only the ``phraw`` sub-directory. The result should look like::

    lib/
        phraw
            extensions/
            phraw.php
        smarty
            plugins/
            sysplugins/
            debug.tpl
            Smarty.class.php
    static/
    media/
    resources/
        cached/
        compiled/
        templates/

Creation of the web site
^^^^^^^^^^^^^^^^^^^^^^^^

Phraw is now ready to use. In order to print something you should create an index.php file. Continue reading the next chapter :ref:`quickstart` for more informations.
