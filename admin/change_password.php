<?php

require_once( '../internals/Header.inc.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();

$key = $_GET['key'];

$selfurl = URL_ADMIN . 'change_password.php?key=' . $key;

if ( empty($key) )
{
    SK_HttpRequest::redirect(URL_ADMIN);
}

$ident = app_Passwords::getIdentificator($key);

if ( $ident != 'admin' )
{
    SK_HttpRequest::redirect(URL_ADMIN);
}

if ( !empty($_POST['change']) )
{
    if ( empty( $_POST['password'] ) )
    {
        $frontend->registerMessage('New password is empty', 'error');
        
        SK_HttpRequest::redirect($selfurl);
    }
    
    if ( $_POST['password'] != $_POST['re_password'] )
    {
        $frontend->registerMessage('Passwords do not match', 'error');
        
        SK_HttpRequest::redirect($selfurl);
    }
    
    $config = SK_Config::section('site')->Section('admin');
    $config->set('admin_password', app_Passwords::hashPassword($_POST['password']));
    
    $frontend->registerMessage('Password changed');
    
    SK_HttpRequest::redirect(URL_ADMIN);
}

$frontend->assign('selfurl', $selfurl);

$frontend->display( 'change_password.html' );