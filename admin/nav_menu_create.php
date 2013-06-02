<?php
$file_key	= 'navigation';
$active_tab	= 'nav_menu';
//$active_tab = ( $_GET['f_page'] ) ? $_GET['f_page'] : 'all';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
//require_once( DIR_ADMIN_INC.'class.admin_language.php' );
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );

//$language =& new AdminLanguage();
//$language->ReadCache();

$frontend = new AdminFrontend();

// require file with specific functions
require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.navigation.php' );

$nav_menu = new AdminNavigation();

if ( @$_POST['create'] )
{
	$item_add = $nav_menu->CreateMenuItem( @$_POST['item_name'], @$_POST['item_label'], @$_POST['item_parent'], @$_POST['item_doc'], @$_POST['item_membership'] );
	switch ( $item_add )	
	{
		case -1:
			$frontend->RegisterMessage( 'Incorrect parent menu item', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Item name label missing', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Item with this name already exists', 'error' );
			break;
		default:
			$frontend->RegisterMessage( 'Item added' );
			redirect( URL_ADMIN.'nav_menu_create.php?item_id='.$item_add );
	}
	redirect( URL_ADMIN.'nav_menu_create.php' );
}

if ( @$_POST['save'] )
{
	switch ( $nav_menu->SaveMenuItem( @$_POST['item_id'], @$_POST['item_parent'], @$_POST['item_doc'], @$_POST['item_membership'] ) )
	{
		case 0:
			$frontend->RegisterMessage( 'Item not changed', 'notice' );
			break;
		case -1:
			$frontend->RegisterMessage( 'Item can not be parent to itself', 'notice' );
			break;
		default:
			$frontend->RegisterMessage( 'Item changed' );
			break;
	}	
	redirect( URL_ADMIN.'nav_menu_create.php?item_id='.@$_POST['item_id'] );
}

// Generate Output

$memberships_arr = AdminMembership::GetAllMembershipTypes();

if ( ( int )@$_GET['item_id'] )
{
	$item_info = $nav_menu->GetMenuItemInfo( @$_GET['item_id'] );
	$frontend->assign_by_ref( 'item_info', $item_info );
	foreach ( $memberships_arr as $key => $mem_info )
	{
		if ( in_array( $mem_info['membership_type_id'], $item_info['memberships'] ) )
			$memberships_arr[$key]['checked'] = true;
	}
}

$frontend->assign_by_ref( 'memberships_arr', $memberships_arr );

$all_menu = $nav_menu->GetMenuItems();

$frontend->assign_by_ref( 'all_menu', $all_menu );

$languages = SK_LanguageEdit::getLanguages();
$frontend->assign_by_ref( 'languages', $languages );

$all_documents = $nav_menu->GetAllDocuments();
$frontend->assign_by_ref( 'all_docs', $all_documents );

//print_arr($all_menu);

$_page['title'] = (( int )@$_GET['item_id']) ? "Edit Menu Item" : "Create Menu Item";

$template = 'nav_menu_create.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'navigation.js' );


$frontend->register_function( 'print_select_parent', 'FrontendPrintMenuSelect' );

// display template
$frontend->display( $template );
?>
