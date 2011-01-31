<?php
function view_example(&$phraw, &$smarty) {
    $contexts = array(
        'url_parameter' => $phraw->request['url_parameter']
    );
    
    // Publish
    $smarty->assign($contexts);
    $smarty->display('module_example/view_example.html');
}
?>