<?php

$file_key = 'blogs';
$active_tab = 'blogs_conf';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );


require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

//$language =& new AdminLanguage();
//$language->ReadCache();

$frontend = new AdminFrontend( $language );

require_once( 'inc.admin_menu.php' );

$_page['title'] = 'Blogs';

$conf = new adminConfig();

$conf->SaveConfigs($_POST);
$conf->getResult($frontend);

$template = 'blogs_conf.html';


// display template
$frontend->display( $template );
?>
