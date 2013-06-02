<?php

require_once '../internals/Header.inc.php';

// Admin authentication
require_once DIR_ADMIN_INC.'fnc.auth.php';

if (!isAdminAuthed()) {
	exit("Athentication required");
}

if ( !isset($_POST['function_']) ) {
	exit('no action');
}

$function_ = $_POST['function_'];
unset($_POST['function_']);


// Action functions
function langResponse_loadSections( array $params )
{
	return json_encode(array(
		'sections' => SK_LanguageEdit::getSections($params['parent_section_id'])
	));
}


function langResponse_loadKeys( array $params )
{
	return json_encode(array(
		'keys' => SK_LanguageEdit::getKeyNodes($params['section_id'], true)
	));
}


function langResponse_createSection( array $params )
{
	$new_section = SK_LanguageEdit::createSection(
		$params['parent_section_id'], $params['section'], $params['description']
	);

	return json_encode(array(
		'section' => $new_section
	));
}


function langResponse_processKeyNodeForm( array $params )
{
	if ( !@$params['lang_key_id'] && !@$params['lang_section_id'] ) {
		exit('error: neither "lang_key_id" nor "lang_section_id" params are undefined');
	}
	if ( !@$params['key'] ) {
		exit('error: undefined param "key"');
	}
	if ( !@$params['values'] ) {
		exit('error: undefined param "values"');
	}

	$existed_key_id = SK_LanguageEdit::getKeyId($params['lang_section_id'], $params['key']);

	if ( $existed_key_id ) {

		// TODO: UPDATE language key...

	}

	// setting key values (function will create a new key if it's need)
	SK_LanguageEdit::setKey(
		$params['lang_section_id'],
		$params['key'],
		(array)$params['values']
	);

	// getting new or existed key node
	$key_node = SK_LanguageEdit::getKeyNode(
		($existed_key_id ? $existed_key_id : SK_LanguageEdit::getKeyId($params['lang_section_id'], $params['key']))
	);

	return json_encode(array('key_node' => $key_node));
}


function langResponse_processKeyEditForm( array $params )
{
	if ( !@$params['lang_key_id'] ) {
		exit('undefined param "lang_key_id"');
	}

	if ( !@$params['key'] ) {
		exit('undefined param "key"');
	}

	SK_LanguageEdit::renameKey($params['lang_key_id'], $params['key']);

	return '{key: "'.$params['key'].'"}';
}


function langResponse_deleteKey( array $params )
{
	if ( !@$params['lang_key_id'] ) {
		exit('undefined param "lang_key_id"');
	}

	SK_LanguageEdit::deleteKeyById($params['lang_key_id']);

	return '{success: true}';
}


function langResponse_loadKeyValuesForEdit( array $params )
{
	$return_data = array();

    $return_data['demoMode'] = (defined('SK_DEMO_MODE') && SK_DEMO_MODE) && !isAdminAuthed(false);

	$lang_id = isset($params["lang_id"]) ? (int) $params["lang_id"] : SK_Config::section('languages')->default_lang_id;

	if ( !($lang_key_id = (int)@$params['lang_key_id']) )
	{
		$lang_section = trim(@$params['lang_section']);
		$lang_key = trim(@$params['lang_key']);
		if ( strlen($lang_section) && strlen($lang_key) ) {
			$lang_key_id = SK_LanguageEdit::getKeyId($lang_section, $lang_key);
			$return_data['lang_key_id'] = $lang_key_id;
		}
		else {
			exit('language key address params missing');
		}
	}

	$key_node = SK_LanguageEdit::getKeyNode($lang_key_id);
	$return_data['values'] = $key_node->values;

	if ( @$params['get_languages'] ) {
		$return_data['languages'] = SK_LanguageEdit::getLanguages();
		$return_data['default_lang_id'] = $lang_id;
	}

	return json_encode($return_data);
}


function langResponse_updateKeyValues( array $params )
{
	if ( !@$params['lang_key_id'] ) {
		exit('undefined param "lang_key_id"');
	}
	if ( !@$params['values'] ) {
		exit('error: undefined param "values"');
	}

	SK_LanguageEdit::updateKeyValues($params['lang_key_id'], $params['values']);

	$key_node = SK_LanguageEdit::getKeyNode($params['lang_key_id'], true);

	return '{values: '.json_encode($key_node->values).'}';
}












if ( !function_exists('langResponse_'.$function_) ) {
	exit("unknown function '$function_'");
}


echo call_user_func('langResponse_'.$function_, $_POST);

