<?php

$file_key = 'languages';
$active_tab = 'langs_packages';

require_once( '../internals/Header.inc.php' );
require_once( DIR_ADMIN_INC .'LanguageMigration.class.php' );
// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );
require_once( DIR_ADMIN_INC.'fnc.lang_search.php' );
require_once( DIR_ADMIN_INC .'fnc.design.php' );
$frontend = new AdminFrontend();
SK_LanguageMigration::createDumpDir();

if ( $_POST['generate_dump'] )
{
	try {
		SK_LanguageExport::exportLanguage($_POST['language_id']);

		$frontend->registerMessage( 'Language dump file was generated!' );
	} catch (SK_LanguageMigrateException $e) {
		$frontend->registerMessage( 'Specify the language!', 'error' );
	}

	redirect( URL_ADMIN.'langs_manage.php' );
}

if ( $_POST['download_dump'] )
{
	if ( !SK_LanguageMigration::downloadFile( $_POST['dump_filename'] ) )
		$frontend->registerMessage( 'Select dump file!', 'error' );
}
if ( $_POST['import_lang'] )
{
	try {

        if ( $_POST['import_type'] == 'insert' && ( empty($_POST['new_lang_label']) || empty($_POST['new_lang_abr']) ) )
        {
            throw new SK_LanguageMigrateException("Undefined language name or abbreviation", 1);
        }

        SK_LanguageImport::import(array(
            "file_name" => 	@$_POST['dump_file'],
            "lang_label"=> 	@$_POST['new_lang_label'],
            "lang_abr"	=> 	@$_POST['new_lang_abr'],
            "lang_id"	=> 	@$_POST['edit_lang_id']
        ));


		$frontend->registerMessage( 'Language was imported successfully' );

	} catch (SK_LanguageMigrateException $e) {
		$frontend->registerMessage( $e->getMessage(), 'error' );
	}

	redirect( URL_ADMIN.'langs_manage.php' );
}
if ( $_POST['upload_dump'] )
{
	if ( in_array( $_FILES['upload_lang_dump']['name'], SK_LanguageMigration::scanDumpDir() ) )
	{
		$frontend->registerMessage( 'The same dump file already exists!', 'notice' );
		redirect( URL_ADMIN.'langs_manage.php' );
	}

	$avaliableTypes = array("text/xml", "application/x-gzip", "application/gzip");

	if ( !in_array($_FILES['upload_lang_dump']["type"], $avaliableTypes) ) {
	    $frontend->registerMessage( 'Language dump uploading failed!', 'error' );
		redirect( URL_ADMIN.'langs_manage.php' );
	}

	$uploadReault = checkUploadedFile( 'upload_lang_dump', 10 * 1024 * 1024, DIR_LANG_DUMP.$_FILES['upload_lang_dump']['name'] );

	if ( $uploadReault > 0 )
		$frontend->registerMessage( 'Language dump was uploaded successfully' );
	else
		$frontend->registerMessage( 'Language dump uploading failed!', 'error' );

	redirect( URL_ADMIN.'langs_manage.php' );
}
if ( $_POST['delete_dump'] )
{
	if ( @unlink( DIR_LANG_DUMP.$_POST['dump_filename'] ) )
		$frontend->registerMessage( 'Language dump file was deleted' );
	else
		$frontend->registerMessage( 'There is error while dump file was deleting', 'error' );
	redirect( URL_ADMIN.'langs_manage.php' );
}
if( $_GET['command'] == 'delete_lang' && (int)$_GET['lang_id'] )
{
	controlAdminGETActions();
	
	$_lang_id = (int)$_GET['lang_id'];
	
	$_query = sql_placeholder( "DELETE FROM `".TBL_LANG_VALUE."` WHERE `lang_id`=?", $_lang_id );
	$_result = MySQL::affectedRows( $_query );
	if( $_result )
		AdminFrontend::registerMessage( $_result . ' values deleted' );
	
	$_query = sql_placeholder( "SELECT `label` FROM `".TBL_LANG."` WHERE `lang_id`=?", $_lang_id );
	$language_name = MySQL::fetchField( $_query );
	
	$_query = sql_placeholder( "DELETE FROM `".TBL_LANG."` WHERE `lang_id`=?", $_lang_id );
	if( MySQL::affectedRows( $_query ) )
		AdminFrontend::registerMessage( '<b>' . $language_name . '</b> language deleted' );
	
	header( 'Location: ' . $_SERVER['PHP_SELF'] );
	exit();
}

if( $_POST )
{
	switch( $_POST['command'] )
	{
		case 'save_configs':
			$result = 0;

			$_POST['def_lang_id'] = (int)$_POST['def_lang_id'];

			$result = SK_Config::section('languages')->set('default_lang_id', $_POST['def_lang_id']);

			$_POST['lang_statuses'][$_POST['def_lang_id']] = 1;

			$_cquery = sql_compile_placeholder( "UPDATE `".TBL_LANG."` SET `enabled`=? WHERE `lang_id`=?" );

			$languages = SK_LanguageEdit::getLanguages();

			foreach ($languages as $lang) {
				$_active = $_POST['lang_statuses'][$lang->lang_id] ? '1' : '0';
				$_query = sql_placeholder( $_cquery, $_active, $lang->lang_id );
				$result += MySQL::affectedRows( $_query );
			}

			if( $result )
				AdminFrontend::registerMessage( 'Preferences saved' );

			break;

		case 'add_new_lang':

			$name = trim( $_POST['lang_name'] );
			$abbr = trim($_POST['lang_abbr']);

			if( !strlen( $name ) ) {
				AdminFrontend::registerMessage( 'Undefined language name', 'error' );
				break;
			}
			if( !strlen( $abbr ) ) {
				AdminFrontend::registerMessage( 'Undefined language abbreviation', 'error' );
				break;
			}

			$_query = sql_placeholder( "INSERT INTO `".TBL_LANG."`(`abbrev`,`label`) VALUES(?, ?)",	$abbr, $name );

			SK_MySQL::query($_query);
			if( SK_MySQL::affected_rows() ) {
				SK_LanguageEdit::generateCache();
				AdminFrontend::registerMessage( 'New language <b>'. $name .'</b> added', 'message' );
			}

			break;

		case 'rename_lang':

			$lang_id = (int)$_GET['lang_id'];
			if( !$lang_id )
			{
				AdminFrontend::registerMessage( 'Undefined language id', 'error' );
				break;
			}

			$new_lang_name = trim( $_POST['lang_name'] );
			if( !strlen( $new_lang_name ) )
			{
				AdminFrontend::registerMessage( 'Undefined new language name', 'error' );
				break;
			}

			$old_lang_name = MySQL::fetchField( 'SELECT `label` FROM `'. TBL_LANG .'` WHERE `lang_id`=' . $lang_id );

			$query = sql_placeholder( 'UPDATE `?#TBL_LANG` SET `label`=? WHERE `lang_id`=?', $new_lang_name, $lang_id );
			if( MySQL::affectedRows( $query ) )
				AdminFrontend::registerMessage( 'Language <b>'. $old_lang_name .' succesfully renamed to <b>'. $new_lang_name .'</b>' );

			break;
	}

	header( 'Location: ' . $_SERVER['REQUEST_URI'] );
	exit;
}

$frontend->IncludeJsFile( URL_ADMIN_JS . 'lang_manage.js' );

adminConfig::SaveConfigs($_POST);

adminConfig::getResult($frontend, false);

$tempVar = SK_LanguageMigration::scanDumpDir();
$frontend->assign_by_ref( 'lang_dumps',  $tempVar);

$_all_lang_info_arr = SK_LanguageEdit::getLanguages();

$frontend->assign_by_ref( '_all_lang_info_arr', $_all_lang_info_arr );

$query = 'SELECT COUNT(`key`) FROM `'. TBL_LANG_KEY .'`';
$lang_keys_num = SK_MySQL::query($query)->fetch_cell();

$query = 'SELECT * FROM `'. TBL_LANG .'`';
$res = SK_MySQL::query($query);

$languages_arr = array();

while ($lang = $res->fetch_assoc())
{
	$missing = getMissingLabels($lang['lang_id']);
	$lang['miss_values'] = $missing['total'];
	
	unset($missing);
	
	$languages_arr[$lang['lang_id']] = $lang;
}

if( is_numeric( $_GET['lang_id'] ) )
{	 
	$selected_language = array
	(
		'id'	=>	$_GET['lang_id'],
		'label'	=>	$languages_arr[$_GET['lang_id']]['label']
	);
		
	$frontend->assign_by_ref( 'selected_language', $selected_language );
}

$lang_configs = SK_Config::section('languages')->get('default_lang_id');

$frontend->assign_by_ref( 'lang_configs', $lang_configs );

$frontend->assign_by_ref( 'languages', $languages_arr );

require_once( 'inc.admin_menu.php' );

$_page['title'] = "Language Packages";

$frontend->includeCSSFile( URL_ADMIN_CSS . 'langs.css' );

$frontend->display( 'langs_manage.html' );

