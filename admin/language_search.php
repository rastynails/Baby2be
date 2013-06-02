<?php

$file_key = 'languages';
$active_tab = 'search';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.lang_search.php' );

$frontend = new AdminFrontend();

$frontend->IncludeJsFile( URL_ADMIN_JS . 'lang_search.js' );

require_once( 'inc.admin_menu.php' );


if( $_GET['command'] == 'search' ) {
	$keys = searchLangKeys($_GET['phrase'], $_GET['search_key'], $_GET['search_value']);
				
	$frontend->assign('search_key', $_GET['phrase']);
	$frontend->assign_by_ref( 'keys', $keys );
}
elseif ( $_GET['command'] == 'missing' && isset($_GET['language_id'])) {
	$keys = getMissingLabels($_GET['language_id']);

	$langs = SK_LanguageEdit::getLanguages();
	
	foreach ($langs as $lang)
	{
		if ($lang->lang_id == $_GET['language_id']) {
			$for_lang = $lang; 
		}
	}
	
	$frontend->assign('lang', $for_lang->label );
	$frontend->assign_by_ref( 'keys', $keys['labels'] );
	$frontend->assign_by_ref( 'missing_count', $keys['total'] );
}


$frontend->page_title = 'Languages Search - SkaDate';

$frontend->registerOnloadJS( "\$('lang_search_input').focus();");

$_page['title'] = "Search";

$frontend->display( 'language_search.html' );

?>
