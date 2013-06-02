<?php

//require_once( DIR_APP.'app.mail.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'AntiBruteforce.class.php' );

function redirect( $url )
{
	header( "Location: $url" );
	exit();
}

function controlAdminAuth()
{
	if ( !isAdminAuthed() )
		redirect( URL_ADMIN.'auth.php' );
}

function controlAdminPOSTActions()
{
	if ( !$_POST )
		return;

	if ( $_SERVER['SCRIPT_FILENAME'] != DIR_ADMIN.'auth.php' && !isAdminAuthed( false ) )
	{
		AdminFrontend::registerMessage( 'No changes made. Demo mode.', 'notice' );
		redirect( $_SERVER['REQUEST_URI'] );
	}
}

function controlAdminGETActions()
{
	if ( !$_GET )
		return ;

	if ( $_SERVER['SCRIPT_FILENAME'] != DIR_ADMIN.'auth.php' && !isAdminAuthed( false ) )
	{
		AdminFrontend::registerMessage( 'No changes made. Demo mode.', 'notice' );
		redirect( $_SERVER['PHP_SELF'] );
	}
}

function isAdminAuthed( $enable_demo = true )
{
	global $_demo_auth;

	$_username = trim( @$_SESSION['administration']['admin_username'] );
	$_password = trim( @$_SESSION['administration']['admin_password'] );

	if ( !$_username || !$_password )
		return false;

	if ( is_array( $_demo_auth ) && $enable_demo )
	{

		if ( $_username == $_demo_auth['username'] && $_password == $_demo_auth['password'] )
			return true;
	}


	$config = SK_Config::section('site')->Section('admin');
	$_db_password = $config->admin_password;

	$_db_username = $config->admin_username;

	if ( ( $_username != $_db_username ) || ( $_password != $_db_password ) )
	{
		$_query = sql_placeholder("SELECT `admin_username` FROM `?#TBL_ADMIN` WHERE `admin_username`=? AND `admin_password`=?", $_username, $_password );
		if(!MySQL::fetchField($_query))
		{
			return false;
		}
	}

	return true;
}

function authAdmin( $username, $password, $enable_demo = true )
{
	global $_demo_auth;

	$username = trim( $username );
	$password = trim( $password );

	if ( !$username )
		return -1;

	if ( !$password )
		return -2;

	$_SESSION['administration']['superadmin'] = false;

	if ( is_array( $_demo_auth ) && $enable_demo )
	{
		if ( $username == $_demo_auth['username'] && $password == $_demo_auth['password'] )
		{
			$_SESSION['administration']['admin_username'] = $username;
			$_SESSION['administration']['admin_password'] = $password;
			return 2;
		}
	}

	if ( AntiBruteforce::getInstance()->isLocked() )
	{
		return -3;
	}

	$config = SK_Config::section('site')->Section('admin');

	$password = app_Passwords::hashPassword($password);

	if ( ( $username != $config->admin_username) || ( $password != $config->admin_password ) )
	{
		$_query = sql_placeholder("SELECT `admin_username` FROM `?#TBL_ADMIN` WHERE `admin_username`=? AND `admin_password`=?", $username, $password );
		if(!MySQL::fetchField($_query)){
		AntiBruteforce::getInstance()->trackTry(false);
		return -3;
		}
	}
	else
	{
		$_SESSION['administration']['superadmin'] = true;
	}


	$_SESSION['administration']['admin_username'] = $username;
	$_SESSION['administration']['admin_password'] = $password;

	if ( app_Cometchat::isActive() )
	{
    	$_SESSION['cometchat_admin_user'] = $username;
        $_SESSION['cometchat_admin_pass'] = $password;
	}

	AntiBruteforce::getInstance()->trackTry(true);
	return 1;
}

function logoutAdmin()
{
	unset( $_SESSION['administration']['admin_username'] );
	unset( $_SESSION['administration']['admin_password'] );
	unset( $_SESSION['administration']['superadmin'] );
}

function restoreAdminLoginInfo( $email )
{
	$email = trim( $email );

	if ( !$email )
		return -1;

	$config = SK_Config::section('site')->Section('admin');

	if ( $email != $config->admin_email )
		return -2;

	$key = app_Passwords::getKey('admin');
    $url = URL_ADMIN . 'change_password.php?key=' . $key;

	$_subject = '{$site_name} administration password recovery';
	$_text = 'Hello!

You got this message because somebody (probably you) tried to restore admin password from SkaDate site software and entered your email here '.URL_ADMIN.'auth.php

Here is your admin account info: ' . $url;

	$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
			->setRecipientEmail($config->admin_email)
			->setPriority(1)
			->setSubject($_subject)
			->setContent($_text);
	app_Mail::send($msg);

	return 1;
}

/*$m[0] = 'JHMgPSBQQUNLQUdFX1ZFUlNJT04uIjsiLlBBQ0tBR0VfVkVSU0lPTi4iOyIuVVJMX0hPTUU7ICRoID0gZm9wZW4oImh0dHA6Ly93d3cuc2thZGF0ZS5jb20vdXBkYXRlcy8/JHMiLCAiciIpOyBmcmVhZCgkaCwgNTEyKTsgZmNsb3NlKCRoKTs=';
$m[1] = array(226, 225, 243, 229, 182, 180, 223, 228, 229, 227, 239, 228, 229);
$m[2] = 'Y3JlYXRlX2Z1bmN0aW9u';*/

/**
 * Test username and password admin demo array info
 */
if (defined("SK_DEMO_MODE") && SK_DEMO_MODE) {
	$_demo_auth = array( 'username' => 'admin', 'password' => 'skadate' );
}

?>
