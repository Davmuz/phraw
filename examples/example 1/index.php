<?php
# Global variables: place here your global settings
define('DEBUG', false); # Debug mode
define('RESOURCES_DIR', 'resources'); # Resources directory path
define('DEFAULT_FROM_EMAIL', 'info@your-email-address.com'); # Your official email address

# Load Phraw with the starter shortcut
require_once('./lib/phraw/phraw.php');
$starter = new DefaultStarter();

# Declare a set of pages
$static_pages = array(
    '' => 'flatpages/home.html',
    'contacts' => 'flatpages/contacts.html'
);

# Routing
if (!$starter->static_route($static_pages)) { # Display a static page or a 404 error
    if ($starter->phraw->route('show\/(?P<url_parameter>\d+)')) {
        require_once('./resources/module_example/views.php');
        view_example($starter->phraw, $starter->template_engine);
    } else {
        $starter->display_error_404();
    }
}
?>