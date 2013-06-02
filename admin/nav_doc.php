<?php
$file_key	= 'navigation';
$active_tab	= 'nav_doc';
//$active_tab = ( $_GET['f_page'] ) ? $_GET['f_page'] : 'all';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

//require_once( DIR_INC.'fnc.custom.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
//require_once( DIR_ADMIN_INC.'class.admin_language.php' );
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );
//require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

//$language = new AdminLanguage();
//$language->ReadCache();

$frontend = new AdminFrontend();

//$_admin_config = new adminConfig();

// require file with specific functions
require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.navigation.php' );

$nav_doc = new AdminNavigation();

if ( @$_POST['save'] )
{
    /*printArr($_POST['doc_access']);
    exit;*/
	if ( $nav_doc->SaveDocumentsPermissions() )
		$frontend->RegisterMessage( 'Permissions changed' );		
	else 
		$frontend->RegisterMessage( 'Permissions not changed', 'notice' );				
	
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_POST['delete'] )
{
	if ( $nav_doc->DeleteDocument( @$_POST['doc_arr'] ) )
		$frontend->RegisterMessage( 'Documents deleted from database' );	
	else 
		$frontend->RegisterMessage( 'Documents not deleted from database', 'notice' );	
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_GET['move'] )
{
	controlAdminGETActions();
	
	switch ( $nav_doc->ChangeDocumentOrder( @$_GET['doc_key'], @$_GET['move'] ) )
	{
		case -3:
			$frontend->RegisterMessage( 'Undefined document key', 'error' );		
			break;
		case -2:
			$frontend->RegisterMessage( 'Can not change order of the document', 'notice' );
		case -1:
			$frontend->RegisterMessage( 'Undefined move action', 'error' );
			break;
		default:
			$frontend->RegisterMessage( 'Document moved' );	
			break;				
	}	
	redirect( $_SERVER['PHP_SELF'] );
}

// Generate Output
$all_documents = $nav_doc->GetDocuments();
$frontend->assign_by_ref( 'documents', $all_documents );

// detect if not base fields exists
$not_base_exist = $nav_doc->IsNotBaseDocExists();
$frontend->assign_by_ref( 'not_base_exist', $not_base_exist );

$_page['title'] = "Documents";

$template = 'nav_doc.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'navigation.js' );

$frontend->register_function( 'print_documents', 'FrontendPrintDocuments' );

// display template
$frontend->display( $template );
?>
