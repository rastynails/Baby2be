<?php

$file_key = 'fbconnect';
$active_tab = 'fbconnect_fields';

require_once( '../internals/Header.inc.php' );
require_once DIR_SITE_ROOT . 'facebook_connect' . DIRECTORY_SEPARATOR . 'init.php';

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );

$adminProfileFields	= new AdminProfileField();
$frontend = new AdminFrontend();

$fbService = FBC_AdminService::getInstance();

require_once( 'inc.admin_menu.php' );

if ( isset($_POST['save_sex_v']) )
{
    SK_Config::section('facebook_connect')->set('sex_aliasing', $_POST['sexV']);

    SK_HttpRequest::redirect(sk_make_url());
}

//Get and adapt input data
$locationFields = AdminProfileField::getLocationFields();

$query = "SELECT `name` FROM `" . TBL_PROFILE_FIELD . "` WHERE (`presentation` IN('text', 'textarea') OR `name`='birthdate') AND `name` NOT IN('" . implode("', '", $locationFields) . "')";

if ($_POST['save'] && !empty($_POST['fields']))
{
    $aliases = $_POST['fields'];
    foreach ($aliases as $q => $f)
    {
        if (empty($f))
        {
            $fbService->removeAssignByQuestion($q);
        }
        else
        {
            $fbService->assignQuestion($q, $f);
        }
    }

    SK_HttpRequest::redirect(sk_make_url());
}

$fields = array();
$result = SK_MySQL::query($query);
while($field = $result->fetch_cell())
{
    $fields[$field] = $fbService->getPossibleFbFieldList($field);
}

$aliases = $fbService->findAliasList();

$frontend->assign('fields', $fields);
$frontend->assign('aliases', $aliases);

$svalues = SK_ProfileFields::get('sex')->values;
$tplSValues = array();
$sexAliasing = SK_Config::section('facebook_connect')->sex_aliasing;
$frontend->assign('sexAliasing', (array) $sexAliasing);

foreach ( $svalues as $v )
{
    $tplSValues[$v] = SK_Language::text('profile_fields.value.sex_' . $v);
}

$frontend->assign('sexValues', $tplSValues);

$_page['title'] = "Facebook Connect";

$frontend->display( 'fbconnect_fields.html' );
