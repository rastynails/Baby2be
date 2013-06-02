<?php

$file_key = 'index_page';

$active_tab = 'templates';

require_once '../internals/Header.inc.php';


// Admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';
require_once DIR_ADMIN_INC.'class.page_builder.php';

require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

require_once 'inc.admin_menu.php';

if ($_POST['activate'])
{
	if (PageBuilder::setActiveTheme($_POST['theme_name']))
		$frontend->registerMessage('Template was activated');
	else 
		$frontend->registerMessage('Template was not activated', 'notice');
		
	redirect( $_SERVER['REQUEST_URI'] );	
}

$themes = PageBuilder::readThemes();
$frontend->assign('themes', $themes);

$current_theme = PageBuilder::getActiveTheme();
$frontend->assign('active_theme', $current_theme);

$frontend->IncludeJsFile(URL_ADMIN_JS.'templates.js');

$frontend->registerOnloadJS(
<<<JSCODE
window.admin_templates = new AdminTemplates(URL_ADMIN_IMG, '{$current_theme['theme_name']}');
JSCODE
);

$_page['title'] = 'Templates';

$frontend->display('templates.html');
