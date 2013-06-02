<?php

$file_key = 'languages';

$active_tab = 'lang_tree';

require_once '../internals/Header.inc.php';

// Admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';

require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

require_once 'inc.admin_menu.php';

$_page['title'] = 'Site Languages';


// processing post data
if ( !empty($_POST) && $_POST['action'] ) {
	require_once DIR_ADMIN_INC.'cache_process.inc.php';
}

$root_sections = json_encode( SK_LanguageEdit::getSections(0) );
$languages = json_encode( SK_LanguageEdit::getLanguages() );

$frontend->IncludeJsFile(URL_STATIC.'jquery.dimensions.js');
$frontend->IncludeJsFile(URL_ADMIN_JS.'lang_edit.js');

$frontend->registerOnloadJS(
<<<JSCODE
window.admin_lang_edit = new AdminLangEdit($root_sections, $languages);
JSCODE
);

$frontend->display('languages_edit.html');
