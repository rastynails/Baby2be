<?php

$file_key	= 'administration';
$active_tab	= 'admin_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

//require_once( DIR_INC.'fnc.custom.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );

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

if ( @$_POST['action'] )
{
	switch ($_POST['action'])
	{
		case 'sadmin_add':
			$_admin_username = $_POST['sadmin_username'];
			$_admin_password = $_POST['sadmin_password'];
			$_admin_email = $_POST['sadmin_email'];

			$SAdminId = addSAdmin($_admin_username, $_admin_password, $_admin_email);
			if( $SAdminId <=0 )
			{
				switch ($SAdminId)
				{
					case -1:
					case -2:
						$frontend->registerMessage( 'There is not enough info for operation', 'error' );
						break;
					case -3:
						$frontend->registerMessage( 'This username is already exists', 'error' );
						break;
					case -4:
						$frontend->registerMessage( 'The additional administrator username and password should differ from the global administrator access details', 'error' );
						break;
				}
				redirect( $_SERVER['PHP_SELF'] );
			}

			$frontend->registerMessage( 'New admin was successfuly added. Please assign new admin section access' );
			redirect( URL_ADMIN.'assign_sections.php?admin_id='.$SAdminId );
			break;

		case 'sadmin_delete':
			$_sadmin_info_set = @$_POST['sadmin_info_set'];

			if(count($_sadmin_info_set))
			{
				foreach ($_sadmin_info_set as $_sadmin_info)
					if(intval($_sadmin_id = $_sadmin_info['admin_id']))
						deleteSAdmin($_sadmin_id);
			}
			else
			{
				$frontend->registerMessage( 'Please, select admins to delete', 'error' );
				redirect( $_SERVER['PHP_SELF'] );
			}

			$frontend->registerMessage( 'Operation was successfuly completed' );
			redirect( $_SERVER['PHP_SELF'] );
			break;
	}
}
//<---
$admin_info_set = getSubAdminInfo();
foreach ($admin_info_set as $_key=>$admin_info)
{
	$_query = sql_placeholder("SELECT `file_key` FROM `?#TBL_LINK_ADMIN_DOCUMENT` WHERE `admin_id`=?", $admin_info['admin_id']);
	foreach (MySQL::fetchArray($_query) as $set)
		$admin_info_set[$_key]['file_keys'][] = $set['file_key'];
}
$sadmin_allowed_section_info_set = getSadminAllowedSections(@$_GET['admin_id']);
foreach ($sidebar_menu_items as $sidebar_menu_item)
	foreach ($sidebar_menu_item['items'] as $_file_key => $_section)
	{
		$_section['access'] = ( isSAdminSectionAccessControl(@$_GET['admin_id'], $_file_key) )? 'y':'n';
		$adm_sections['root_sections'][$sidebar_menu_item['key']][$_file_key] = $_section;
	}

$frontend->assign('adm_sections',$adm_sections);
$frontend->assign('admin_info_set',$admin_info_set);

// Generate Output
$_page['title'] = "Administrators";

$template = 'administration.html';

// display template
$frontend->display( $template );
