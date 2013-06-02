<?php
$file_key	= 'navigation';
$active_tab	= 'nav_doc';
//$active_tab = ( $_GET['f_page'] ) ? $_GET['f_page'] : 'all';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

//require_once( DIR_INC.'fnc.custom.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
//require_once( DIR_ADMIN_INC.'class.admin_language.php' );
require_once( DIR_ADMIN_INC.'class.admin_ads.php' );
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );

//$language =& new AdminLanguage();
//$language->ReadCache();

$frontend = new AdminFrontend();

// require file with specific functions
require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.navigation.php' );

$nav_doc = new AdminNavigation();

if ( @$_POST['create'] )
{
	$create_doc = $nav_doc->CreateDocument( $_POST );
	
	switch ( $create_doc )
	{
		case -1:
			$frontend->RegisterMessage( 'Empty document key', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Empty label for document', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'URL for document does not exist', 'error' );		
			break;
		case -4:
			$frontend->RegisterMessage( 'Document with the same key already exists', 'error' );
			break;
		case -5:
			$frontend->RegisterMessage( 'Custom page with this Url already exists', 'error' );
			break;
		default:
			$frontend->RegisterMessage( 'Document created' );
			//$language->GenerateCache();
			redirect( URL_ADMIN.'nav_doc_create.php?doc_key='.$create_doc );
	}	
	
	redirect( URL_ADMIN.'nav_doc_create.php' );
}

if ( @$_POST['save'] )
{
	$save_doc = $nav_doc->SaveDocument( $_POST );
	switch ( $save_doc )
	{
		case -1:
			$frontend->RegisterMessage( 'Empty document key', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'URL for document does not exist', 'error' );		
			break;
		case -5:
			$frontend->RegisterMessage( 'Custom page with this Url already exists', 'error' );
			break;
		default:
			if ( $save_doc )
			{
				$frontend->RegisterMessage( 'Document changed' );
				//$language->GenerateCache();
			}
			else 
				$frontend->RegisterMessage( 'Document not changed', 'notice' );			
	}
	redirect( URL_ADMIN.'nav_doc_create.php?doc_key='.$_POST['doc_key'] );
}

// Generate Output

if ( @$_GET['doc_key'] )
{
	$doc_info = $nav_doc->GetDocumentInfo( $_GET['doc_key'] );	
	$frontend->assign_by_ref( 'doc_info', $doc_info );
}


$all_documents = $nav_doc->GetDocuments('',@$_GET['doc_key']);

$frontend->assign_by_ref( 'documents', $all_documents );

$languages = SK_LanguageEdit::getLanguages();

$frontend->assign_by_ref( 'languages', $languages );

$_page['title'] = (@$_GET['doc_key']) ? "Edit Document" : "Create Document";

$template = 'nav_doc_create.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
//$frontend->IncludeJsFile( URL_MAIN_JS.'form.js' );
//$frontend->IncludeJsFile( URL_MAIN_JS.'_header.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'navigation.js' );

$frontend->register_function( 'editor_links', array(&$frontend, 'tpl_CustomPageEditorLinks') );
$frontend->IncludeJsFile( URL_STATIC . 'ckeditor/ckeditor.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS . 'custom_page_editor.js' );

foreach ( $languages as $lang )
{
    if ( $lang->default )
    {
        $frontend->registerJSCode( 'var defaultLangId = ' . $lang->lang_id );
        break;
    }
}


// register function
$frontend->register_function( 'print_select_parent', 'FrontendPrintDocumentSelect' );

// display template
$frontend->display( $template );
?>
