<?php

define('DIR_INTERNALS', dirname(__FILE__).DIRECTORY_SEPARATOR);

// including setup configs
require_once DIR_INTERNALS.'config.php';

if ( !defined('DIR_SITE_ROOT') ) {
	define('DIR_SITE_ROOT', dirname(DIR_INTERNALS).DIRECTORY_SEPARATOR);
}

if ( !defined('DEV_MODE') ) {
	define('DEV_MODE', false);
}

// including constants definition
require_once DIR_INTERNALS.'system.const.php';

require_once DIR_INTERNALS.'CoreAPI.func.php';
require_once DIR_INTERNALS.'Debug.func.php';
require_once DIR_INTERNALS.'ErrorHandler.inc.php';
require_once DIR_UTILS.'debug.php';
require_once DIR_UTILS.'profiler.php';

/**
 * Component factory.
 * Instantiates a component object.
 *
 * @param string $name
 * @param array $params
 * @return SK_Component
 */
function SK_Component( $name, array $params = null )
{
	global $SK_ComponentReplacements;

	if ( isset($SK_ComponentReplacements[$name]) ) {
		$name = $SK_ComponentReplacements[$name];
	}

	$class = 'component_'.$name;

	return new $class($params);
}

/**
 * PHP5 core function __autoload() implementation
 * for automagically including SkaDate7 API classes.
 *
 * @param string $class
 */
function __autoload( $class )
{
	switch (true)
	{
		case (strpos($class, 'SK_') === 0):
			$filename = DIR_API.substr($class, 3).'.class.php';
			break;

		case (strpos($class, 'app_') === 0):
			$filename = DIR_APPS.substr($class, 4).'.app.php';
			break;

		case ( strpos($class, 'component_') === 0
			&& preg_match('/^component_(\w+)(_responder)?$/iU', $class, $match)
			): $filename = DIR_COMPONENTS.$match[1].'.cmp.php';
			break;

		case ( strpos($class, 'httpdoc_') === 0
			&& preg_match('/^httpdoc_(\w+)$/', $class, $match)
			): $filename = DIR_HTTPDOCS.$match[1].'.httpdoc.php';
			break;

		case (strpos($class, 'nav_') === 0):
			$filename = DIR_NAV_MODULES.substr($class, 4).'.nav.php';
			break;

		case ( strpos($class, 'form_') === 0
			&& preg_match('/^form_(\w+)$/', $class, $match)
			): $filename = DIR_FORMS.$match[1].'.form.php';
			break;

		case ( strpos($class, 'field_') === 0
			&& preg_match('/^field_(\w+)$/', $class, $match)
			): $filename = DIR_FORM_FIELDS.$match[1].'.field.php';
			break;

		case ( strpos($class, 'fieldType') === 0
			&& preg_match('/^fieldType_(\w+)$/', $class, $match)
			): $filename = DIR_FORM_FIELD_TYPES.$match[1].'.field.php';
			break;

		case ( strpos($class, 'formAction') === 0
			&& preg_match('/^formAction_(\w+)$/', $class, $match)
			): $filename = DIR_FORM_ACTIONS.$match[1].'.action.php';
			break;

		default:
			return;
	}

	require_once $filename;
}
SK_Profiler::getInstance('app');

// database tables definition file
require_once DIR_INTERNALS.'db_tbl.const.php';

// including old MySQL class and sql_placeholder functionality for backward compatibility
require_once DIR_SK6_INC.'class.mysql.php';
require_once DIR_SK6_INC.'fnc.sql_placeholder.php';

require_once DIR_INTERNALS.'str.func.php';

require_once DIR_INTERNALS.'url.func.php';


/**
 * Recurcively clears a strings in $var_entry of slashes.
 *
 * @param string|array $var
 * @return string|array
 */
function r_stripSlashes( $var_entry )
{
	if ( is_string($var_entry) ) {
		return stripslashes($var_entry);
	}
	elseif ( is_array($var_entry) ) {
		foreach ( $var_entry as $key => $value ) {
			$var_entry[$key] = r_stripSlashes($value);
		}
		return $var_entry;
	}
	else {
		return false;
	}
}

if ( ini_get('magic_quotes_gpc') )
{
	$_GET = r_stripSlashes($_GET);
	$_POST = r_stripSlashes($_POST);
	$_COOKIE = r_stripSlashes($_COOKIE);
}

// connecting SQL server
SK_MySQL::connect();

try
{
    if ( SK_Config::section('cloudflare')->enable )
    {
        if ( isset($_SERVER['HTTP_CF_CONNECTING_IP']) )
        {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
    }
}
catch (SK_ConfigException $e){}

define( 'URL_CHUPPO_VHOST', SK_Config::section('chuppo')->get('vhost') );

define( 'URL_CHUPPO_RECORDER_SERVER', URL_CHUPPO_VHOST.'vmess/' );
define( 'URL_CHUPPO_PLAYER_SERVER', URL_CHUPPO_VHOST.'vmess_player/' );
define( 'URL_CHUPPO_CHAT_SERVER', URL_CHUPPO_VHOST.'chuppochat/' );
define( 'URL_CHUPPO_USERON_SERVER', URL_CHUPPO_VHOST.'useronim/' );
define( 'URL_CHUPPO_IM_SERVER', URL_CHUPPO_VHOST.'im/' );

// remote servers
define('AUTOCROP_SERVICE_URL', "http://crop.goofytalk.com/crop.asmx");


define( 'URL_FLASH_SERVER', 'rtmp://www.dotsilver.com/' );
define( 'URL_CHUPPO_PLAYER', 'http://swf.chuppomedia.com/' );
define( 'URL_CHUPPO_RECORDER', ' http://swf.chuppomedia.com/' );
define( 'URL_CHUPPO_CHAT', 'http://swf.chuppomedia.com/' );
define( 'URL_CHUPPO_USERON', 'http://swf.chuppomedia.com/' );
define( 'URL_CHUPPO_IM', 'http://swf.chuppomedia.com/' );


define('PACKAGE_VERSION', "9.2.2960");



SK_Navigation::setTrustedDir(URL_ADMIN);
SK_Navigation::setTrustedDir(URL_AFFILIATE);
SK_Navigation::setTrustedDir(URL_STATIC);
SK_Navigation::setTrustedDir(URL_CHECKOUT);
SK_Navigation::setTrustedDir(SITE_URL . 'update/');
//SK_Navigation::setTrustedDir(SITE_URL . 'games/');
SK_Navigation::setTrustedDir(SITE_URL . 'install/');
SK_Navigation::setTrustedDir(SITE_URL . 'm/');
SK_Navigation::setTrustedDir(SITE_URL . 'facebook_connect/');
SK_Navigation::setTrustedDir(SITE_URL . 'facebook/');
SK_Navigation::setTrustedDir(SITE_URL . 'google/');

$novel_games_trusted_files = array(
    'games/common.php',
    'games/startPlaying.php',
    'games/gameEnded.php',
    'games/Game.php',
    'games/getAllInfo.php',
    'games/getGameInfo.php',
    'games/getMessages.php',
    'games/getSiteInfo.php',
    'games/invite.php',
    'games/joinRoom.php',
    'games/joinTable.php',
    'games/leaveRoom.php',
    'games/openTable.php',
    'games/Player.php',
    'games/restart.php',
    'games/restartSubmit.php',
    'games/robotJoinTable.php',
    'games/config.php',
    'games/Room.php',
    'games/test.php',
    'games/sendChatMessage.php',
    'games/sendEmail.php',
    'games/sendGameMessage.php',
    'games/Site.php',
    'games/Table.php'

);

foreach($novel_games_trusted_files as $file)
{
    SK_Navigation::setTrustedFile(SITE_URL . $file);
}


SK_Navigation::setTrustedFile(SITE_URL . "file_uploader.php");
SK_Navigation::setTrustedFile(SITE_URL . "field_responder.php");
SK_Navigation::setTrustedFile(SITE_URL . "form_processor.php");
SK_Navigation::setTrustedFile(SITE_URL . "xml_http_responder.php");
SK_Navigation::setTrustedFile(SITE_URL . "123chat_login.php");
SK_Navigation::setTrustedFile(SITE_URL . "finvite.php");
SK_Navigation::setTrustedFile(URL_CHECKOUT . "ICEPAY/pre_checkout.php");
SK_Navigation::setTrustedFile(SITE_URL . "member/splash_screen.php");



// get the timezone setting from database
date_default_timezone_set(SK_Config::section('site')->Section('official')->time_zone);

if ( !(defined('IS_CRON') && IS_CRON) )
{
    if ( !(defined('IS_CRON') && IS_CRON) )
    {
        if ( defined('SITE_URL') && !SK_Navigation::isCurrentArea(SITE_URL . 'install/')) {
            $s_url_info = parse_url(SITE_URL);
            if ($_SERVER['HTTP_HOST'] != $s_url_info['host']) {
                SK_HttpRequest::redirect(sk_make_url());
            }
        }
    }

    // starting an http user session
    SK_HttpUser::session_start();

    if ( isset($_GET['from']) && $_GET['from'] == 'mobile' )
    {
        $_SESSION['from_mobile'] = true;
    }

	// check config for mobile version
	$redirect = SK_Config::section('mobile')->get('redirect_to_mv');

	SK_UserAgent::setup();

	if ( $redirect && empty($_SESSION['from_mobile']) )
	{
		if ( SK_UserAgent::get('is_mobile') && !SK_Navigation::isCurrentArea(SITE_URL . 'admin/') )
		{
			$dir = DIR_SITE_ROOT . SK_Config::section('mobile')->get('mobile_directory') . DIRECTORY_SEPARATOR;
			$conf_file = $dir . 'mconfig.php';
			if (file_exists($conf_file) )
			{
				require_once $conf_file;
				if ( defined('MOBILE_SITE_DOMAIN') && strlen(MOBILE_SITE_DOMAIN) )
					SK_HttpRequest::redirect(MOBILE_SITE_DOMAIN);
			}
		}
	}

	if ( !(defined("IS_NAVIGATION") && IS_NAVIGATION) && !SK_Navigation::isAreaTrusted(sk_make_url()) && !strstr($_SERVER['REQUEST_URI'], 'login_wm.php')) {

		$result = SK_HttpRequest::prepare(sk_request_uri());

		if ( empty($result) && !SK_HttpRequest::isXMLHttpRequest() ) {
			SK_HttpRequest::redirect(SK_Navigation::getDocument('not_found')->url);
		}
	}

	app_Affiliate::AffiliateProgram();

	require_once DIR_INTERNALS . "NavModules.inc.php";

	$require_path = SK_HttpRequest::getRequarePath();

	if ( isset($require_path) ) {
		require_once($require_path);
		exit();
	}
}
