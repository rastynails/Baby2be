<?php

$file_key	= 'administration';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

//require_once( DIR_INC.'fnc.custom.php' );
//require_once( DIR_ADMIN_INC.'fnc.design.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
//require_once( DIR_ADMIN_INC.'class.admin_language.php' );
//require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );
//require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'fnc.subadmin.php');
require_once( DIR_ADMIN_INC.'fnc.sadmin_manage.php');

//$language =& new AdminLanguage();
//$language->ReadCache();

$frontend = new AdminFrontend();

// require file with specific functions
require_once( 'inc.admin_menu.php' );
//require_once( DIR_ADMIN_INC.'fnc.navigation.php' );

//$nav_menu =& new AdminNavigation();
if(!intval($_admin_id=@$_GET['admin_id']))
	exit();
switch (@$_POST['action'])	
{
	case 'assign_sections':
		
		$_query = sql_placeholder("DELETE FROM `?#TBL_LINK_ADMIN_DOCUMENT` WHERE `admin_id`=?", $_admin_id);
		MySQL::fetchResource($_query);
		if(count($_POST['sections']))
		{

			foreach ($_POST['sections'] as $_file_key=>$status)
			{
				$_query = sql_placeholder("INSERT INTO `?#TBL_LINK_ADMIN_DOCUMENT`(`file_key`,`admin_id`) VALUES(?,?)", $_file_key, $_admin_id);
				MySQL::fetchResource($_query);
			}
		}
		$frontend->registerMessage('Admin sections were assigned');
		redirect($_SERVER['REQUEST_URI']);
		break;

	case 'sadmin_info_change':
		if( !strlen($_sadmin_username = $_POST['sadmin_username']) || 
				( strlen($_sadmin_email = $_POST['sadmin_email']) && 
				!preg_match('/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-]+?.[a-zA-Z0-9_]{2,}(\.\w{2})?$/i', $_sadmin_email )
			)
		){
			$frontend->registerMessage('Please, check entries for accurancy');
			redirect($_SERVER['REQUEST_URI']);	
		}
		
		$_sadmin_password = @$_POST['sadmin_password'];
		
		if( updateSAdminInfo($_admin_id, $_sadmin_username, $_sadmin_password, $_sadmin_email) > 0 )
			$frontend->registerMessage('Admin info was updated');
		else 
			$frontend->registerMessage('Admin info was not updated.');
		redirect($_SERVER['REQUEST_URI']);
		
		break;
}
		
//<---
$sadmin_info_set = getSubAdminInfo($_admin_id);
$sadmin_allowed_section_info_set = getSadminAllowedSections($_admin_id);
foreach ($sidebar_menu_items as $sidebar_menu_item)
	foreach ($sidebar_menu_item['items'] as $_file_key => $_section)
	{
		if($_file_key == 'administration')
			continue;
		$_section['access'] = ( isSAdminSectionAccessControl($_admin_id, $_file_key) )? 'y':'n';
		$adm_sections['root_sections'][$sidebar_menu_item['key']][$_file_key] = $_section;
	}				
$frontend->assign('adm_sections',$adm_sections);
//$frontend->assign('admin_info_set',$admin_info_set);
$frontend->assign('sadmin_info_set',$sadmin_info_set);
//<---
// Generate Output

$_page['title'] = "Administrators";

$template = 'assign_sections.html';
//page vars
// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
//$frontend->IncludeJsFile( URL_MAIN_JS.'form.js' );
//$frontend->IncludeJsFile( URL_MAIN_JS.'_header.js' );
//$frontend->IncludeJsFile( URL_ADMIN_JS.'navigation.js' );

//$frontend->register_function( 'print_items', 'frontendPrintMenuItems' );

// display template
$frontend->display( $template );
?>