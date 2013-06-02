<?php

$file_key = 'index_page';

$active_tab = 'home_page';

require_once '../internals/Header.inc.php';

// Admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';
require_once DIR_ADMIN_INC.'class.page_builder.php';

require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

require_once 'inc.admin_menu.php';

$current_theme = PageBuilder::getActiveTheme();

if ( isset ($_POST['action']) )
{
	switch ( $_POST['action'] )
	{
		case 'save_code':
			if ( PageBuilder::updateCode($_POST['theme_name'], 'home_page_code', $_POST['page_code']) )
			{
				$frontend->registerMessage('Page markup was updated');
			}
			else 
			{
                $frontend->registerMessage('Page markup was not updated. No changes made', 'notice');
			}
			
			break;
			
		case 'reset_code':
			if ( PageBuilder::resetCode($current_theme['theme_name'], 'home_page_code', false) )
			{
				$frontend->registerMessage('Code was reset');
			}
			break;
	}
	
	redirect( $_SERVER['REQUEST_URI'] );
}

$frontend->assign('active_theme', $current_theme);
$frontend->IncludeJsFile( URL_ADMIN_JS.'index_page.js' );

$_page['title'] = "Home Page Builder";

$frontend->display('home_page.html');
