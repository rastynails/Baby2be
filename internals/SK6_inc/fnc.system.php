<?php

/**
 * Project:    SkaDate
 * File:       fnc.system.php
 *
 * Description...
 * 
 * @link http://www.skadate.com/
 * @package SkaDate
 * @version 4.0
 */

/**
 * Strips slashes recursively
 *
 * @param mixed $var
 */
function clearSlashes( $var )
{
	if( is_array( $var ) )
	{
		foreach( $var as $_k => $_v )
			$var[$_k] = clearSlashes( $_v );
	}
	else
	{
		$var = stripslashes( $var );
	}
	
	return $var;
}


/**
 * Updates config var value if <var>$name</var> found in database
 * or insert new record
 *
 * @param string $name
 * @param string $value
 * @return integer affected rows
 */

function saveConfigVar( $name, $value )
{
	return MySQL::fetchField( "SELECT `name` FROM `".TBL_CONFIG."` WHERE `name`='$name'" )
			? MySQL::affectedRows( "UPDATE `".TBL_CONFIG."` SET `value`='$value' WHERE `name`='$name'" )
			: MySQL::affectedRows( "INSERT INTO `".TBL_CONFIG."`(`name`,`value`) VALUES('$name','$value')" );
}

/**
 * Returns config vars value
 *
 * @param string $name
 * @return mixed
 */
function getConfig( $name )
{
	static $_config;
	
	if( !$_config )
		$_config = MySQL::fetchArray( "SELECT `name`,`value` FROM `".TBL_CONFIG."` WHERE 1", 1 );
	
	if( !isset( $_config[$name] ) )
		fail_error( 'Undefined config var <code>'.$name.'</code>' );
	
	return $_config[$name];
}


/**
* Checks if the feature is an available.
*
* @param integer $feature_id
* @return boolean
*/
function isFeatureAvailable( $feature_id )
{
	static $_features;
	
	if ( !$_features )
		$_features = MySQL::fetchArray( "SELECT `feature_id`,`name` FROM `".TBL_FEATURE."` WHERE `active`='yes'", 1 );

	return ( $_features[$feature_id] )? true : false;
}

/**
 * Returns an available layouts info array.
 *
 * @return array
 */
function getLayoutsArray()
{
	$_layouts_dir = opendir( DIR_LAYOUTS );
	
	$layouts = array();
	
	while( false !== ( $_f = readdir( $_layouts_dir ) ) )
	{
		if( $_f == '.' || $_f == '..' || !file_exists( DIR_LAYOUTS . $_f . '/layout.php' ) )
			continue;

		$_LAYOUT_INFO = array();
		
		include( DIR_LAYOUTS . $_f . '/layout_info.php' ); // layout.php contains array $_LAYOUT_INFO
		
		$layouts[$_f] = $_LAYOUT_INFO;
	}
	
	closedir( $_layouts_dir );
	
	ksort( $layouts );
	
	return $layouts;
}

/**
 * Get current active layout/
 * This method try to find active layout in $_GET 
 * and $_COOKIE array
 *
 * @return string
 */
function getLayout()
{
	static $layout;
	
	if( !defined( 'DIR_LAYOUTS' ) )
		FatalError( 'Undefined constant <code>DIR_LAYOUTS</code>"<br />file <code>' . __FILE__ .' line ' . __LINE__ );
	
	if( strlen( $layout ) )
		return $layout;
	
	if( strlen( $_GET['layout'] ) )
	{
		$_layout_php = DIR_LAYOUTS . $_GET['layout'] . '/layout.php';
		
		if( $_GET['layout'] != getConfig( 'default_layout' ) && file_exists( $_layout_php ) )
		{
			$layout = $_GET['layout'];
			setcookie( 'layout', '', 0, PATH_HOME );
			setcookie( 'layout', $layout, time()+2592000, PATH_HOME );
		}
		else
		{
			resetUserLayout();
		}
	}
	elseif( !empty( $_COOKIE['layout'] ) )
	{
		$_layout_php = DIR_LAYOUTS . $_COOKIE['layout'] . '/layout.php';
		
		if( file_exists( $_layout_php ) )
			$layout = $_COOKIE['layout'];
		else
			resetUserLayout();
	}
	
	if( !strlen( $layout ) )
		$layout = getConfig( 'default_layout' );
	
	return $layout;
}

/**
 * Reset current active layout
 *
 */
function resetUserLayout()
{
	setcookie( 'layout', '', 0, PATH_HOME );
	
	$_request_url = $_SERVER['PHP_SELF'] . '?';
		
		foreach ( $_GET as $_key => $_value )
		{
			if( $_key != 'layout' )
			{
				if( !is_array( $_value ) )
				{
					$_request_url .= $_key . '=' . $_value . '&';
				}
				else
				{
					foreach( $_value as $_k => $_v )
						$_request_url .= $_key[$_k] . '=' . $_v;
				}
			}
		}
	
	header( 'Location: ' . $_request_url );
	exit();
}


/**
 * Prints fatal error message
 * and stop script execution
 *
 * @access public
 * @param string $message
 */
function FatalError( $message )
{
	print '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
	<title>Fatal Error</title>
	<meta http-equiv="content-type" content="text/html" />
</head>

<style>

.fatal_error
{
	margin: 6px 2px;
	color: #7f000b;
	font-family: Tahoma;
	font-size: 11px;
	font-weight: bold;
	letter-spacing: 1px;
}

.fatal_error b
{
	color: #bf0015;
	font-family: Verdana;
	font-size: 13px;
	font-weight: bold;
}

.fatal_error code
{
	margin: 2px;
	color: #10253f;
	font-size: 11px;
	font-weight: normal;
}

</style>

<body>
	<div class="fatal_error">
		'.$message.'
		<br />
	</div>
<body>

</html>
';
		
	exit;
}

/**
 * Return the number of seconds
 * after that the profile will be logged out without inactivity
 *
 * @return integer
 */
function getExpirationTime()
{
	return time()+getConfig( 'member_logout_sec' );
}

/**
 * Creates file with content
 * in _cache folder.
 *
 * @param string $file_name
 * @param string $content
 * @return boolean
 */
function createInCacheFile( $file_name, $content )
{
	$_file_path = DIR_CACHE.$file_name;
	
	if ( !file_exists( $_file_path ) )
	{
		$_f = @fopen( $_file_path, 'w' );
		
		if ( $_f )
		{
			fputs( $_f, $content );
			return true;
		}
	}
	
	return false;
}

/**
 * Creates specfied dir
 * in _cache folder.
 * Created folder has 777 permissions.
 * Use this method to write some file in _cache folder
 *
 * @param string $dir_name
 */
function createInCacheDir( $dir_name )
{
	if ( !file_exists( DIR_CACHE.'/'.$dir_name ) )
		mkdir( DIR_CACHE.'/'.$dir_name, 0777 );
}

/**
 * Set document key for current page
 *
 */
function setDocumentKey()
{
	global $document_key;

	$_SESSION['current_page_key'] = ( $document_key )? $document_key : $_SESSION['current_page_key'];
}

/**
 * Returns the current document key
 * Use this method to find the document key
 *
 * @return string
 */
function getDocumentKey()
{
	global $document_key;

	return ( $document_key )? $document_key : $_SESSION['current_page_key'];
}

/**
 * Cut the domain name from 
 * specified URL
 *
 * @param string $url
 * @return string
 */
function getDomainFromURL( $url )
{
	 preg_match( '/^http:\/\/([\w\.\-]+)[:\d]*\//msi', $url, $matches );
	 return $matches[1];
}

/**
 * Returns the REMOTE_ADDR var in $_SERVER global array
 *
 * @return string
 */
function getRemoteClientIP()
{
	return $_SERVER['REMOTE_ADDR'];
}
?>