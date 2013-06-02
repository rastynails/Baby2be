<?php

$file_key	= 'navigation';
$active_tab	= 'nav_menu';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

//require_once( DIR_INC.'fnc.custom.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );


$frontend = new AdminFrontend();

// require file with specific functions
require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.navigation.php' );

$nav_menu = new AdminNavigation();

$memberships_arr = AdminMembership::GetAllMembershipTypes();

if ( @$_POST['save'] )
{
	if ( $nav_menu->SaveMenuPermissions( $memberships_arr ) )
		$frontend->RegisterMessage( 'Permissions changed' );		
	else 
		$frontend->RegisterMessage( 'Permissions not changed', 'notice' );				
	
	redirect( $_SERVER['REQUEST_URI'] );	
}

if ( @$_POST['delete'] )
{
	try {
		if ( $nav_menu->DeleteMenuItems( @$_POST['item_arr'] ) )
			$frontend->RegisterMessage( 'Menu items deleted' );
		else 
			$frontend->RegisterMessage( 'Menu items not deleted', 'notice' );
	} catch (SK_AdminNavigationException $e) {
		$frontend->RegisterMessage( $e->getMessage(), 'notice' );
	}
		
	redirect( $_SERVER['REQUEST_URI'] );	
}

if ( @$_GET['move'] )
{
	controlAdminGETActions();
	
	switch ( $nav_menu->ChangeMenuItemOrder( @$_GET['item'], @$_GET['move'] ) )	
	{
		case -1:
			$frontend->RegisterMessage( 'Undefined move', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Can not move item', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Undefined menu item', 'error' );
		case 1:
			$frontend->RegisterMessage( 'Menu item moved' );		
	}
	
	redirect( $_SERVER['PHP_SELF'] );
}

// Generate Output

$all_menu = $nav_menu->GetMenuItems();

$frontend->assign_by_ref( 'all_menu', $all_menu );

$not_base_item = $nav_menu->IsNotBaseItemExists();
$frontend->assign_by_ref( 'not_base_item', $not_base_item );

$frontend->assign_by_ref( 'memberships_arr', $memberships_arr );

//print_arr($all_menu);

$_page['title'] = "Navigation Menu";

$template = 'nav_menu.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'navigation.js' );

$frontend->IncludeJsFile( URL_ADMIN_JS . 'jquery-1.7.2.min.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS . 'jquery-ui-1.8.16.custom.min.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS . 'jquery.mjs.nestedSortable.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS . 'dnd_navigation.js' );
$frontend->includeCSSFile( URL_ADMIN_CSS . 'drag_and_drop.css' );

$frontend->register_function( 'print_items', 'frontendPrintMenuItems' );

// display template
$frontend->display( $template );

?>
