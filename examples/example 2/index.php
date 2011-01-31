<?php
# Global variables: place here your global settings
define('DEBUG', false); # Debug mode
define('RESOURCES_DIR', 'resources'); # Resources directory path
define('DEFAULT_FROM_EMAIL', 'info@your-email-address.com'); # Your official email address

set_include_path('./lib' . PATH_SEPARATOR . get_include_path());

# Load Phraw with the starter shortcut
require_once('phraw/phraw.php');
$phraw = new Phraw();

# Load the Smarty extension
require_once('phraw/extensions/smarty.php');
$smarty = new SmartyTemplateEngine();

# Declare a set of pages
$static_pages = array(
    '' => 'flatpages/home.html',
    'contacts' => 'flatpages/contacts.html'
);

# Routing
$static_page = static_route($static_pages, $phraw); # The matching parameters will be in $phraw->request
if ($static_page) {
    $smarty->display($static_page);
} else if ($phraw->route('show\/(?P<url_parameter>\d+)')) {
    require_once('./resources/module_example/views.php');
    view_example($phraw, $smarty);
} else {
    $smarty->display_error_404();
}
?>