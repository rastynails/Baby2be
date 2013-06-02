<?php

/**
 * Sign out page
 *
 * @package SkaDate7
 * @version 7.0
 * @since 7.0
 * @link http://www.skadate.com/
 */

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$document_key = SK_HttpRequest::getDocument()->document_key;


if ( SK_HttpUser::is_authenticated() ) {
	app_Profile::deleteCookiesLogin(SK_HttpUser::username());
}

SK_HttpUser::logoff();

if ( ($document_redirect = SK_Config::section('navigation')->Section('settings')->signout_document_redirect) != $document_key )
{
	SK_HttpRequest::redirect( SK_Navigation::href($document_redirect));
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>You have been logged out</title>
		<style>p{ font-family: Verdana; font-size: 11px }</style>
	</head>
	
	<body>
		<p><?php
		echo SK_Language::text('%msg.sign_out', array( 'site_index_url' => SK_Navigation::href( 'index' ) ) );
		?></p>
	</body>
</html>