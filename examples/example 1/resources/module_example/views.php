<?php
function view_example(&$phraw, &$smarty) {
    $contexts = array(
        'url_value' => $phraw->uri_values['url_value']
    );
    
    // Publish
    $smarty->assign($contexts);
    $smarty->display('module_example/view_example.html');
}
?>