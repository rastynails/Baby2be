<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

if ( !isset($_POST['form']) || !preg_match('~^\w+$~i', $_POST['form']) ) {
	trigger_error('unrecognized form', E_USER_ERROR);
}

if ( !isset($_POST['action']) || !preg_match('~^\w+$~i', $_POST['action']) ) {
	trigger_error('unrecognized action', E_USER_ERROR);
}

$form_name = $_POST['form'];
$form_action = $_POST['action'];
$form_data = json_decode(urldecode($_POST['data']), true);

$include_path = DIR_FORMS_C . $form_name . '.form.php';

if ( DEV_MODE && !file_exists($include_path) ) {
	trigger_error('unrecognized form "'.$form_name.'"', E_USER_ERROR);
}

require $include_path;

$response = new SK_FormResponse();

$return = $form->process($form_data, $form_action, $response);

$response->process($return);
