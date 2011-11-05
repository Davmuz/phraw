<?php
# Global variables: place here your global settings
define('DEBUG', false); # Debug mode
#define('RESOURCES_DIR', 'resources'); # Default resources directory path, you can ovverride this uncommenting it

# Load Phraw
require_once('lib/phraw/phraw.php');
$phraw = new Phraw();
$phraw->add_include_path('lib'); # Add the "lib" directory to the include path

# Load Smarty through the Phraw extension
require_once('phraw/extensions/smarty.php');
$smarty = new SmartyTemplateEngine();

# Prepare a bulk of static pages.
$static_pages = array(
    'wellcome' => 'flatpages/wellcome.html',
    'contacts' => 'flatpages/contacts.html'
);

# Routing
if ($phraw->route('')) { # Home page
    $smarty->display('flatpages/home.html');

} else if ($phraw->detect_no_trailing_slash()) { # Detect and fix the trailing slash for the following routed pages
    $phraw->fix_trailing_slash();
    
} else if ($phraw->bulk_route($static_pages, $page_found)) { # Fill $page_found if a $static_pages page if found
    $smarty->display($page_found);
    
} else if ($phraw->route('^show\/(?P<url_value>\d+)\/?$')) { # Get "url_value" from the URL and load a custom view function
    require_once('./resources/module_example/views.php');
    view_example($phraw, $smarty);
    
} else {
    $smarty->display_error();
}
?>