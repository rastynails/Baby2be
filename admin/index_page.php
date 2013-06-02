<?php

$file_key = 'index_page';

$active_tab = 'index_page';

require_once '../internals/Header.inc.php';


// Admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';
require_once DIR_ADMIN_INC.'class.page_builder.php';

require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

require_once 'inc.admin_menu.php';

$current_theme = PageBuilder::getActiveTheme();

if (isset ($_POST['action']) )
{
	switch ($_POST['action'])
	{
		case 'save_code':
			if ( PageBuilder::updateCode($_POST['theme_name'], 'index_page_code', $_POST['page_code']) )
				$frontend->registerMessage('Index page markup was updated');
			else 
				$frontend->registerMessage('Index page markup was not updated. No changes made','notice');
			redirect( $_SERVER['REQUEST_URI'] );
			break;
			
		case 'reset_code':
			if ( PageBuilder::resetCode($current_theme['theme_name'], 'index_page_code') )
				$frontend->registerMessage('Code was reset');
			redirect( $_SERVER['REQUEST_URI'] );
			break;
	}	
}



$frontend->assign('active_theme', $current_theme);
$frontend->IncludeJsFile( URL_ADMIN_JS.'index_page.js' );

$_page['title'] = "Index Page Builder";

$frontend->display('index_page.html');
